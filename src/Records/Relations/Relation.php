<?php

namespace Nip\Records\Relations;

use Nip\Database\Connection;
use Nip\Records\_Abstract\Table;
use Nip_DB_Query_Select as Query;
use Nip_Record as Record;
use Nip_RecordCollection as RecordCollection;
use Nip_Records as Records;

abstract class Relation
{

    protected $_name;

    /**
     * @var Record
     */
    protected $_item;

    /**
     * @var Records
     */
    protected $_manager = null;


    /**
     * @var Records
     */
    protected $_with = null;

    protected $_table = null;
    protected $_fk = null;

    /**
     * @var Query
     */
    protected $_query;


    protected $_populated = false;

    protected $_params = array();

    protected $_results = null;

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

    public function setItem(Record $item)
    {
        $this->_item = $item;
        return $this;
    }

    /**
     * @return Record
     */
    public function getItem()
    {
        return $this->_item;
    }

    /**
     * @return Records
     */
    public function getManager()
    {
        if ($this->_manager == null) {
            $this->initManager();
        }
        return $this->_manager;
    }

    public function initManager()
    {
        $this->_manager = $this->getItem()->getManager();
    }

    /**
     * @param Table $manager
     */
    public function setManager($manager)
    {
        $this->_manager = $manager;
    }

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

    public function initQuery()
    {
        $query = $this->newQuery();
        $this->populateQuerySpecific($query);

        $this->_query = $query;
    }

    public function populateQuerySpecific(Query $query)
    {
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

    /**
     * @return \Nip_DB_Query_Select
     */
    public function newQuery()
    {
        return $this->getWith()->paramsToQuery();
    }

    /**
     * @return Connection
     */
    public function getDB()
    {
        return $this->getManager()->getDB();
    }

    public function setParams($params)
    {
        $this->_params = $params;
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

    public function checkParamWith($params)
    {
        if (isset($params['with'])) {
            $this->setWith($params['with']);
            unset($params['with']);
        }
    }

    public function checkParamTable($params)
    {
        if (isset($params['table'])) {
            $this->setTable($params['table']);
            unset($params['table']);
        }
    }

    public function checkParamFk($params)
    {
        if (isset($params['fk'])) {
            $this->setFK($params['fk']);
            unset($params['fk']);
        }
    }


    /**
     * @return Records
     */
    public function getWith()
    {
        if ($this->_with == null) {
            $this->initWith();
        }
        return $this->_with;
    }

    public function initWith()
    {
        $className = inflector()->pluralize($this->getName());
        $this->setWithClass($className);
    }

    /**
     * @param string $name
     */
    public function setWithClass($name)
    {
        $object = call_user_func(array($name, "instance"));
        $this->setWith($object);
    }

    /**
     * @param Records $object
     * @return $this
     */
    public function setWith(Records $object)
    {
        $this->_with = $object;
        return $this;
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

    public function initTable()
    {
        $tableName = $this->getWith()->getTable();
        $this->setTable($tableName);
    }

    public function setTable($name)
    {
        $this->_table = $name;
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

    public function initFK()
    {
        $name = $this->getManager()->getPrimaryFK();
        $this->setFK($name);
    }

    public function setFK($name)
    {
        $this->_fk = $name;
    }

    /**
     * @return string
     */
    public function getWithPK()
    {
        return $this->getWith()->getPrimaryKey();
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

    public function setResults($results)
    {
        $this->_results = $results;
        $this->_populated = true;
        return $this->_results;
    }

    public function isPopulated()
    {
        return $this->_populated == true;
    }

    abstract public function initResults();

    public function getEagerResults($collection)
    {
        $query = $this->getEagerQuery($collection);
        return $this->getWith()->findByQuery($query);
    }

    /**
     * @param RecordCollection $collection
     * @return \Nip_DB_Query_Select
     */
    public function getEagerQuery(RecordCollection $collection)
    {
        $fkList = $this->getEagerFkList($collection);
        $query = clone $this->getQuery();
        $query->where($this->getWithPK() . ' IN ?', $fkList);
        return $query;
    }

    /**
     * @param RecordCollection $collection
     * @return array
     */
    public function getEagerFkList(RecordCollection $collection)
    {
        return \Nip_Helper_Array::instance()->pluck($collection, $this->getFK());
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

    abstract function getResultsFromCollectionDictionary($dictionary, $collection, $record);

    /**
     * Build model dictionary keyed by the relation's foreign key.
     *
     * @param RecordCollection $collection
     * @return array
     */
    abstract protected function buildDictionary(RecordCollection $collection);
}