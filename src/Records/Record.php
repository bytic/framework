<?php

class Nip_Record extends \Nip\Records\_Abstract\Row {


    protected $_fields = array();
    protected $_dbData = array();
    protected $_helpers = array();

    public function __construct() {
        parent::__construct();
    }

    /**
     * Overloads Ucfirst() helper
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments) {
        if ($name === ucfirst($name)) {
            $class = 'Nip_Helper_' . $name;

            if (!isset($this->helpers[$class])) {
                $this->_helpers[$class] = new $class;
            }
            return $this->_helpers[$class];
        }

        trigger_error("Call to undefined method $name", E_USER_ERROR);
    }

    public function __set($name, $value) {
        parent::__set($name, $value);
        if ($this->getManager()->hasField($name) && !in_array($name, $this->getFields())) {
            $this->_fields[] = $name;
        }
    }

    public function __unset($name) {
        parent::__unset($name);
        unset($this->_fields[array_search($name, $this->_fields)]);
    }

    public function writeDBData($data = false) {
        foreach ($data as $key => $value) {
            $this->_dbData[$key] = $value;
        }
    }

    public function getPrimaryKey() {
        $pk = $this->getManager()->getPrimaryKey();
        return $this->$pk;
    }

    public function insert() {
        $pk = $this->getManager()->getPrimaryKey();
        $this->$pk = $this->getManager()->insert($this);
        $this->_fields = array();
        return $this->$pk > 0;
    }

    public function update() {
        $return = $this->getManager()->update($this);
        $this->_fields = array();
        return $return;
    }

    public function save() {
        $this->getManager()->save($this);
    }

    public function saveRecord() {
        $this->getManager()->save($this);
    }

    public function delete() {
        $this->getManager()->delete($this);
    }

    public function getFields() {
        return $this->_fields;
    }

    public function isInDB() {
        $pk = $this->getManager()->getPrimaryKey();
        return $this->$pk > 0;
    }

}