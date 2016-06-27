<?php

namespace Nip;

class HelperBroker
{
    protected $_helpers = array();

    public function hasHelper($name)
    {
        $name = self::getNameKey($name);
        return isset($this->_helpers[$name]);
    }

    public static function getNameKey($name)
    {
        return strtolower($name);
    }

    public static function get($name)
    {
        $broker = self::instance();
        return $broker->getByName($name);
    }

    public function getByName($name)
    {
        $name = self::getNameKey($name);
        if (!$this->hasHelper($name)) {
            $this->initHelper($name);
        }

        return $this->_helpers[$name];
    }

    public function initHelper($name)
    {
        $this->_helpers[$name] = $this->generateHelper($name);
    }

    public function generateHelper($name)
    {
        $class = $this->getHelperClass($name);
        $helper = new $class;
        return $helper;
    }

    public function getHelperClass($name)
    {
        return 'Nip_Helper_' . ucfirst($name);
    }

    /**
     * Singleton
     * @return self
     */
    static public function instance()
    {
        static $instance;
        if (!($instance instanceof self)) {
            $instance = new self();
        }
        return $instance;
    }

}
