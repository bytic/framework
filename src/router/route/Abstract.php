<?php
abstract class Nip_Route_Abstract
{
    protected $_map;
    protected $_parts;
    protected $_params = array();
    protected $_matches = array();

    public function  __construct($map = false, $params = array())
    {
        if ($map) {
            $this->setMap($map);
        } elseif ($this->_map) {
            $this->parseMap();
        }


        if ($params) {
            $this->setParams($params);
        }
        $this->init();
    }

    public function init()
    {
    }

    public function setMap($map)
    {
        $this->_map   = $map;
        $this->parseMap();
    }

    protected function parseMap()
    {
        $this->_parts = explode("/", $this->_map);
    }
    
    public function setParams($params = array())
    {
        if ($params) {
            foreach ($params as $key => $value) {
                $this->_params[$key] = $value;
            }
        }
    }


    public function assemble($params = array())
    {
        $return = $this->_map;

        if ($params) {
            foreach ($params as $key => $value) {
                if (stristr($return, ":" . $key) !== false) {
                    $return = str_replace(":" . $key, $value, $return);
                    unset($params[$key]);
                }
                if (array_key_exists($key, $this->_params)) {
                    unset($params[$key]);
                }
            }
            if ($params) {
//                $params = array_map('htmlentities', $params);
                $return .= "?" . http_build_query($params);
            }

        }

        // set defaults
        if ($this->_params) {
            foreach ($this->_params as $key => $value) {
                if (is_string($value)) {
                    $return = str_replace(":" . $key, $value, $return);
                }
            }
        }

        return $return;
    }

    public function stripEmptyParams($params)
    {
        if (is_array($params)) {
            foreach ($params as $key => $param) {
                if (empty($param)) {
                    unset($params[$key]);
                } elseif (is_array($param)) {
                    $newParams = $this->stripEmptyParams($param);
                    if (!is_array($newParams) OR count($newParams) < 1) {
                        unset($params[$key]);
                    } else {
                        $params[$key] = $newParams;
                    }
                }
            }
        }
        return $params;
    }

    public function getMap()
    {
        return $this->_map;
    }

    public function getParts()
    {
        return $this->_parts;
    }

    public function getParams()
    {
        return $this->_params;
    }

    public function getMatches()
    {
        return $this->_matches;
    }

    public function setModule($module)
    {
        $this->_module = $module;
    }

    public function setController($controller)
    {
        $this->_controller = $controller;
    }

    public function setAction($action)
    {
        $this->_action = $action;
    }
}