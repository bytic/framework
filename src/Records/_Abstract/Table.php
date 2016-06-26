<?php

namespace Nip\Records\_Abstract;

abstract class Table
{

    protected $_model = null;
    protected $_controller = null;

    public function __construct()
    {
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
    protected function inflect()
    {
        $this->inflectModel();
        $this->inflectController();
    }

    protected function inflectModel()
    {
        $class = get_class($this);
        if ($this->_model == null) {
            $this->_model = $this->generateModelClass($class);
        }
    }

    protected function inflectController()
    {
        $class = get_class($this);
        if ($this->_controller == null) {
            $this->_controller = inflector()->unclassify($class);
        }
    }

    public function generateModelClass($class = null)
    {
        $class = $class ? $class : get_class($this);

        if (strpos($class, '\\')) {
            $nsParts = explode('\\', $class);
            $class = array_pop($nsParts);
            if ($class == 'Table') {
                return implode($nsParts, '\\') . '\Row';
            }
        }
        return ucfirst(inflector()->singularize($class));
    }

    /**
     * @param null $model
     */
    public function setModel($model)
    {
        $this->_model = $model;
    }

    public function getModel()
    {
        return $this->_model;
    }

    public function getController()
    {
        return $this->_controller;
    }

    /**
     * @return Nip_Registry
     */
    public function getRegistry()
    {
        if (!$this->_registry) {
            $this->_registry = new \Nip_Registry();
        }
        return $this->_registry;
    }
}