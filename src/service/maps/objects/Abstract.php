<?php

/**
 * Nip Framework
 *
 * @category   Nip
 * @copyright  2009 Nip Framework
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 */

abstract class Nip_Service_Maps_Objects_Abstract
{
    protected $_service;
    protected $_listeners = [];
    protected $_params = [];

    public function __construct()
    {
    }

    /**
     * @param string $key
     */
    public function setParam($key, $value)
    {
        $this->_params[$key] = $value;
        return $this;
    }

    public function getParam($key)
    {
        return $this->_params[$key];
    }
    
    public function addListener($event, $function)
    {
        $this->_listeners[$event][] = $function;
    }

    public function getListeners()
    {
        return $this->_listeners;
    }

    public function getType()
    {
        $name = str_replace('Nip_Service_Maps_Objects_', '', get_class($this));
        $name = inflector()->hyphenize($name);
        return $name;
    }

    /**
     * @return Nip_Service_Maps
     */
    public function getService()
    {
        return $this->_service;
    }

    public function setService($service)
    {
        $this->_service = $service;

        return $this;
    }
}
