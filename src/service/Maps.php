<?php

/**
 * Nip Framework
 *
 * @category   Nip
 * @copyright  2009 Nip Framework
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 */

class Nip_Service_Maps {

    protected $_api_key;
    protected $_provider;
    protected $_providerObj;

    protected $_params = array();
    protected $_objects = array();

    public function  __construct() {        
    }

    public function  __call($name,  $arguments) {
        if (strpos($name, 'render') === 0) {
            return call_user_func_array(array($this->getProvider(), $name), $arguments);
        }
    }

    public function setApiKey($key) {
        $this->_api_key = $key;
        return $this;
    }

    public function getApiKey() {
        return $this->_api_key;
    }

    public function setProvider($name) {
        $this->_provider = $name;
        return $this;
    }

    public function initProvider() {
        if ($this->_provider) {
            $class = 'Nip_Service_Maps_Provider_' . ucfirst($this->_provider);
            $this->_providerObj = new $class();
            $this->_providerObj->setService($this);
            return true;
        }
        trigger_error('No provider set for ' . get_class($this));
    }

    /**
     * @return Nip_Service_Maps_Provider_Abstract
     */
    public function getProvider() {
        if (!$this->_providerObj) {
            $this->initProvider();
        }
        return $this->_providerObj;
    }

    public function addObject(Nip_Service_Maps_Objects_Abstract $object) {
        $this->_objects[] = $object;
        return $this;
    }

    public function getNewObject($name) {
        $name = 'Nip_Service_Maps_Objects_' . ucfirst($name);
        $object = new $name();
        $object->setService($this);
        return $object;
    }

    public function getObjects() {
        return $this->_objects;
    }

    public function setParam($key, $value) {
        $this->_params[$key] = $value;
        return $this;
    }

    public function getParam($key) {
        return $this->_params[$key];
    }

    public function render() {
        return $this->getProvider()->render();
    }
}
