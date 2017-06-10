<?php

namespace Nip\Records\Relations;

use Nip\Database\Connections\Connection;
use Nip\Database\Query\AbstractQuery;
use Nip\Database\Query\Select as Query;
use Nip\HelperBroker;
use Nip\Logger\Exception;
use Nip\Records\AbstractModels\Record as Record;
use Nip\Records\AbstractModels\RecordManager;
use Nip\Records\Collections\Collection;
use Nip\Records\Collections\Collection as RecordCollection;
use Nip\Records\Traits\Relations\HasRelationsRecordsTrait;
use Nip_Helper_Arrays as ArraysHelper;

/**
 * Class Relation
 * @package Nip\Records\Relations
 */
abstract class Relation
{

    /**
     * @var
     */
    protected $name;

    /**
     * @var string
     */
    protected $type = 'relation';

    /**
     * @var Record
     */
    protected $item;

    /**
     * @var RecordManager
     */
    protected $manager = null;


    /**
     * @var RecordManager
     */
    protected $with = null;

    /**
     * @var null|string
     */
    protected $table = null;

    /**
     * @var null|string
     */
    protected $fk = null;

    /**
     * @var Query
     */
    protected $query;

    /**
     * @var bool
     */
    protected $populated = false;

    /**
     * @var array
     */
    protected $params = [];

    /**
     * @var null|Collection|Record
     */
    protected $results = null;

    /**
     * @return Query
     */
    public function getQuery()
    {
        if ($this->query == null) {
            $this->initQuery();
        }

        return $this->query;
    }

    /**
     * @param $query
     * @return static
     */
    public function setQuery($query)
    {
        $this->query = $query;

        return $this;
    }

    public function initQuery()
    {
        $query = $this->newQuery();
        $this->populateQuerySpecific($query);

        $this->query = $query;
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
        if ($this->with == null) {
            $this->initWith();
        }

        return $this->with;
    }

    /**
     * @param RecordManager|HasRelationsRecordsTrait $object
     * @return $this
     */
    public function setWith($object)
    {
        $this->with = $object;

        return $this;
    }

    public function initWith()
    {
        $className = $this->getWithClass();
        $this->setWithClass($className);
    }

    /**
     * @return mixed
     */
    public function getWithClass()
    {
        return inflector()->pluralize($this->getName());
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param string $name
     * @throws Exception
     */
    public function setWithClass($name)
    {
        $object = call_user_func([$name, "instance"]);
        if (is_object($object) && $object instanceof RecordManager) {
            $this->setWith($object);
        } else {
            throw new Exception(
                "Cannot instance records [" . $name . "] in relation for [" . $this->getManager()->getClassName() . "]"
            );
        }
    }

    /**
     * @return RecordManager
     */
    public function getManager()
    {
        if ($this->manager == null) {
            $this->initManager();
        }

        return $this->manager;
    }

    /**
     * @param RecordManager|HasRelationsRecordsTrait $manager
     */
    public function setManager($manager)
    {
        $this->manager = $manager;
    }

    public function initManager()
    {
        $this->manager = $this->getItem()->getManager();
    }

    /**
     * @return Record
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @param Record $item
     * @return $this
     */
    public function setItem(Record $item)
    {
        $this->item = $item;

        return $this;
    }

    /**
     * @param AbstractQuery $query
     */
    public function populateQuerySpecific(AbstractQuery $query)
    {
    }

    /**
     * @return \Nip\Database\Query\Delete
     */
    public function getDeleteQuery()
    {
        $query = $this->getWith()->newDeleteQuery();
        $this->populateQuerySpecific($query);

        return $query;
    }

    /**
     * @return Connection
     */
    public function getDB()
    {
        return $this->getManager()->getDB();
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getParam($key)
    {
        return $this->hasParam($key) ? $this->params[$key] : null;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function hasParam($key)
    {
        return isset($this->params[$key]);
    }

    /**
     * @param $params
     */
    public function addParams($params)
    {
        $this->checkParamClass($params);
        $this->checkParamWith($params);
        $this->checkParamTable($params);
        $this->checkParamFk($params);
        $this->setParams($params);
    }

    /**
     * @param $params
     */
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
        $this->params = $params;
    }

    /**
     * @return string
     */
    public function getTable()
    {
        if ($this->table == null) {
            $this->initTable();
        }

        return $this->table;
    }

    /**
     * @param $name
     */
    public function setTable($name)
    {
        $this->table = $name;
    }

    protected function initTable()
    {
        $this->setTable($this->generateTable());
    }

    /**
     * @return string
     */
    protected function generateTable()
    {
        return $this->getWith()->getTable();
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

        return $this->results;
    }

    /**
     * @param $results
     * @return null
     */
    public function setResults($results)
    {
        $this->results = $results;
        $this->populated = true;

        return $this->results;
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
        $query->where($this->getWithPK() . ' IN ?', $fkList);

        return $query;
    }

    /**
     * @param RecordCollection $collection
     * @return array
     */
    public function getEagerFkList(RecordCollection $collection)
    {
        /** @var ArraysHelper $arrayHelper */
        $arrayHelper = HelperBroker::get('Arrays');
        $return = $arrayHelper->pluck($collection, $this->getFK());

        return array_unique($return);
    }

    /**
     * @return string
     */
    public function getFK()
    {
        if ($this->fk == null) {
            $this->initFK();
        }

        return $this->fk;
    }

    /**
     * @param $name
     */
    public function setFK($name)
    {
        $this->fk = $name;
    }

    protected function initFK()
    {
        $this->setFK($this->generateFK());
    }

    /**
     * @return string
     */
    protected function generateFK()
    {
        return $this->getManager()->getPrimaryFK();
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
        return $this->type;
    }
}
