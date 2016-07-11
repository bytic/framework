<?php

namespace Nip\Records\_Abstract;

abstract class Row extends \Nip_Object
{

    protected $_name = null;
    protected $_manager = null;
    protected $_managerName = null;

    public function __construct() {

    }

    /**
     * @return mixed
     */
    public function getName()
    {
        if ($this->_name == null) {
            $this->_name = inflector()->unclassify(get_class($this));
        }
        return $this->_name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->_name = $name;
    }


    public function toArray()
    {
        $vars = get_object_vars($this);
        return $vars['_data'];
    }

    public function writeData($data = false)
    {
        foreach ($data as $key => $value) {
            $this->__set($key, $value);
        }
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
        $class = $this->getManagerName();
        $this->_manager = call_user_func(array($class, 'instance'));
    }

    public function setManager($manager)
    {
        $this->_manager = $manager;
    }

    public function getManagerName()
    {
        if ($this->_managerName == null) {
            $this->inflectManagerName();
        }
        return $this->_managerName;
    }

    public function initManagerName()
    {
        $this->_managerName = $this->inflectManagerName();
    }

    public function inflectManagerName()
    {
        return ucfirst(inflector()->pluralize(get_class($this)));
    }
}