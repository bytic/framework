<?php

abstract class Nip_Record_Abstract extends Nip_Object {

    protected $_name;
    protected $_manager;
    protected $_managerName;

    public function __construct($data = array()) {
        $this->_name = inflector()->unclassify(get_class($this));

        if ($data) {
            $this->writeData($data);
        }
    }

    public function toArray() {
        $vars = get_object_vars($this);
        return $vars['_data'];
    }

    public function writeData($data = false) {
        foreach ($data as $key => $value) {
            $this->__set($key, $value);
        }
    }

    /**
     * @return Nip_Records
     */
    public function getManager() {
        if (!$this->_manager) {
            $class = (!empty($this->_managerName)) ? $this->_managerName : ucfirst(inflector()->pluralize(get_class($this)));
            $this->_manager = call_user_func(array($class, 'instance'));
        }

        return $this->_manager;
    }

    public function setManager($manager) {
        $this->_manager = $manager;
    }
}