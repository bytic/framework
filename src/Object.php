<?php

class Nip_Object
{
    protected $_data;

    public function &__get($name)
    {
        if (!$this->__isset($name)) {
            $this->_data[$name] = null;
        }
        return $this->_data[$name];
    }

    public function __set($name, $value)
    {
        $this->_data[$name] = $value;
    }

    public function __isset($name)
    {
        return isset($this->_data[$name]);
    }

    public function __unset($name)
    {
        unset($this->_data[$name]);
    }
}
