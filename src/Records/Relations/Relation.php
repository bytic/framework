<?php

namespace Nip\Records\Relations;

use Nip\Database\Connection;
use Nip\Database\Query\Select as Query;
use Nip\HelperBroker;
use Nip\Records\AbstractModels\Record as Record;
use Nip\Records\AbstractModels\RecordManager;
use Nip\Records\Collections\Collection as RecordCollection;

abstract class Relation
{

    protected $_name;

    protected $_type = 'relation';

    /**
     * @var Record
     */
    protected $_item;

    /**
     * @var RecordManager
     */
    protected $_manager = null;


    /**
     * @var RecordManager
     */
    protected $_with = null;

    protected $_table = null;
    protected $_fk = null;

    /**
     * @var Query
     */
    protected $_query;


    protected $populated = false;

    protected $_params = [];

    protected $_results = null;

    /**
     * @return Query
     */
    public function getQuery()
    {
        if ($this->_query == null) {
            $this->initQuery();
        }

        return $this->_query;
    }

    /**
     * @param $query
     * @return static
     */
    public function setQuery($query)
    {
        $this->_query = $query;

        return $this;
    }

    public function initQuery()
    {
        $query = $this->newQuery();
        $this->populateQuerySpecific($query);

        $this->_query = $query;
    }

    /**
     * @return Query
     */
    public function newQuery()
    {
        return $this->getWith()->paramsToQuery();
    }

    /**
     * @return RecordManager
     */
    public function getWith()
    {
        if ($this->_with == null) {
            $this->initWith();
        }

        return $this->_with;
    }

    /**
     * @param RecordManager $object
     * @return $this
     */
    public function setWith(RecordManager $object)
    {
        $this->_with = $object;

        return $this;
    }

    public function initWith()
    {
        $className = $this->getWithClass();
        $this->setWithClass($className);
    }

    public function getWithClass()
    {
        return inflector()->pluralize($this->getName());
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->_name = $name;
    }

    /**
     * @param string $name
     */
    public function setWithClass($name)
    {
        $object = call_user_func(array($name, "instance"));
        if ($object) {
            trigger_error("Cannot instance records [".$name."] in relation", E_USER_WARNING);
        }
        $this->setWith($object);
    }

    /**
     * @param Query $query
     */
    public function populateQuerySpecific(Query $query)
    {
    }

    /**
     * @return Connection
     */
    public function getDB()
    {
        return $this->getManager()->getDB();
    }

    /**
     * @return RecordManager
     */
    public function getManager()
    {
        if ($this->_manager == null) {
            $this->initManager();
        }

        return $this->_manager;
    }

    /**
     * @param RecordManager $manager
     */
    public function setManager($manager)
    {
        $this->_manager = $manager;
    }

    public function initManager()
    {
        $this->_manager = $this->getItem()->getManager();
    }

    /**
     * @return Record
     */
    public function getItem()
    {
        return $this->_item;
    }

    public function setItem(Record $item)
    {
        $this->_item = $item;

        return $this;
    }

    public function getParam($key)
    {
        return $this->_params[$key];
    }

    public function addParams($params)
    {
        $this->checkParamClass($params);
        $this->checkParamWith($params);
        $this->checkParamTable($params);
        $this->checkParamFk($params);
        $this->setParams($params);
    }

    public function checkParamClass($params)
    {
        if (isset($params['class'])) {
            $this->setWithClass($params['class']);
            unset($params['class']);
        }
    }

    /**
     * @param $params
     */
    public function checkParamWith($params)
    {
        if (isset($params['with'])) {
            $this->setWith($params['with']);
            unset($params['with']);
        }
    }

    /**
     * @param $params
     */
    public function checkParamTable($params)
    {
        if (isset($params['table'])) {
            $this->setTable($params['table']);
            unset($params['table']);
        }
    }

    /**
     * @param $params
     */
    public function checkParamFk($params)
    {
        if (isset($params['fk'])) {
            $this->setFK($params['fk']);
            unset($params['fk']);
        }
    }

    /**
     * @param $params
     */
    public function setParams($params)
    {
        $this->_params = $params;
    }

    /**
     * @return string
     */
    public function getTable()
    {
        if ($this->_table == null) {
            $this->initTable();
        }

        return $this->_table;
    }

    /**
     * @param $name
     */
    public function setTable($name)
    {
        $this->_table = $name;
    }

    public function initTable()
    {
        $tableName = $this->getWith()->getTable();
        $this->setTable($tableName);
    }

    /**
     * Get the results of the relationship.
     * @return Record|RecordCollection
     */
    public function getResults()
    {
        if (!$this->isPopulated()) {
            $this->initResults();
        }

        return $this->_results;
    }

    /**
     * @param $results
     * @return null
     */
    public function setResults($results)
    {
        $this->_results = $results;
        $this->populated = true;

        return $this->_results;
    }

    /**
     * @return bool
     */
    public function isPopulated()
    {
        return $this->populated == true;
    }

    abstract public function initResults();

    /**
     * @param RecordCollection $collection
     * @return RecordCollection
     */
    public function getEagerResults($collection)
    {
        if ($collection->count() < 1) {
            return $this->getWith()->newCollection();
        }
        $query = $this->getEagerQuery($collection);

        return $this->getWith()->findByQuery($query);
    }

    /**
     * @param RecordCollection $collection
     * @return Query
     */
    public function getEagerQuery(RecordCollection $collection)
    {
        $fkList = $this->getEagerFkList($collection);
        $query = $this->newQuery();
        $query->where($this->getWithPK().' IN ?', $fkList);

        return $query;
    }

    /**
     * @param RecordCollection $collection
     * @return array
     */
    public function getEagerFkList(RecordCollection $collection)
    {
        $return = HelperBroker::get('Arrays')->pluck($collection, $this->getFK());

        return array_unique($return);
    }

    /**
     * @return string
     */
    public function getFK()
    {
        if ($this->_fk == null) {
            $this->initFK();
        }

        return $this->_fk;
    }

    public function setFK($name)
    {
        $this->_fk = $name;
    }

    public function initFK()
    {
        $name = $this->getManager()->getPrimaryFK();
        $this->setFK($name);
    }

    /**
     * @return string
     */
    public function getWithPK()
    {
        return $this->getWith()->getPrimaryKey();
    }

    /**
     * @param RecordCollection $collection
     * @param RecordCollection $records
     *
     * @return RecordCollection
     */
    public function match(RecordCollection $collection, RecordCollection $records)
    {
        $dictionary = $this->buildDictionary($records);

        foreach ($collection as $record) {
            /** @var Record $record */
            $results = $this->getResultsFromCollectionDictionary($dictionary, $collection, $record);
            $record->getRelation($this->getName())->setResults($results);
        }

        return $records;
    }

    /**
     * Build model dictionary keyed by the relation's foreign key.
     *
     * @param RecordCollection $collection
     * @return array
     */
    abstract protected function buildDictionary(RecordCollection $collection);

    /**
     * @param $dictionary
     * @param $collection
     * @param $record
     * @return mixed
     */
    abstract public function getResultsFromCollectionDictionary($dictionary, $collection, $record);

    public function save()
    {
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }
}
