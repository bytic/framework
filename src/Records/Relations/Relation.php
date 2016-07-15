<?php

namespace Nip\Records\Relations;

abstract class Relation
{

    protected $_name;

    /**
     * @var \Nip_Record
     */
    protected $_item;

    /**
     * @var \Nip_Records
     */
    protected $_manager = null;


    /**
     * @var \Nip_Records
     */
    protected $_with = null;

    protected $_table = null;
    protected $_fk = null;

    /**
     * @var \Nip_DB_Query_Abstract
     */
    protected $_query;


    protected $_populated = false;

    protected $_params = array();

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

    public function setItem(\Nip_Record $item)
    {
        $this->_item = $item;
        return $this;
    }

    /**
     * @return \Nip_Record
     */
    public function getItem()
    {
        return $this->_item;
    }

    /**
     * @return \Nip_Records
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
     * @param \Nip_Records $manager
     */
    public function setManager($manager)
    {
        $this->_manager = $manager;
    }

    public function getQuery()
    {
        if ($this->_query == null) {
            $this->initQuery();
        }
        return $this->_query;
    }

    public function initQuery()
    {
        $query = $this->getWith()->paramsToQuery();
        $this->populateQuerySpecific($query);

        $this->_query = $query;
    }

    public function populateQuerySpecific($query)
    {
    }

    /**
     * @return self
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
     * @return \Nip_DB_Wrapper
     */
    public function getDB()
    {
        return $this->getManager()->getDB();
    }

    public function addParams($params)
    {
        $this->checkParamClass($params);
        $this->checkParamWith($params);
        $this->checkParamTable($params);
        $this->checkParamFk($params);

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
     * @return \Records
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

    public function setWithClass($name)
    {
        $object = call_user_func(array($name, "instance"));
        $this->setWith($object);
    }

    public function setWith($object)
    {
        $this->_with = $object;
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
}