<?php

abstract class Nip_Records_Abstract {

    protected $_model;
    protected $_controller;
    
    public function __construct() {
        $this->inflect();
    }   
    
    public function getNewRecord($data = array())
    {
        $model = $this->getModel();
        $record = new $model($data);
        $record->setManager($this);        
        return $record;
    }

        /**
     * Sets model and database table from the class name
     */
    protected function inflect() {
        $class = get_class($this);

        if (!$this->_model) {
            $this->_model = ucfirst(inflector()->singularize($class));
        }
        $this->_controller = inflector()->unclassify($class);
    }
    
    public function getModel() {
        return $this->_model;
    }

    public function getController() {
        return $this->_controller;
    }    
    
    /**
     * @return Nip_Registry
     */
    public function getRegistry()
    {
        if (!$this->_registry) {
            $this->_registry = new Nip_Registry();
        }
        return $this->_registry;
    }
}