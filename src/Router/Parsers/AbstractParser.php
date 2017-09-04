<?php

namespace Nip\Router\Parsers;

/**
 * Class AbstractParser
 * @package Nip\Router\Parsers
 */
abstract class AbstractParser
{
    /**
     * @var \Nip\Request
     */
    protected $request;

    /**
     * @var string
     */
    protected $uri;

    /**
     * @var string
     */
    protected $map;

    /**
     * @var array
     */
    protected $parts;

    /**
     * @var array
     */
    protected $params = [];

    /**
     * @var array
     */
    protected $variables = [];

    /**
     * @var array
     */
    protected $matches = [];

    /**
     * AbstractParser constructor.
     * @param bool $map
     * @param array $params
     */
    public function __construct($map = false, $params = [])
    {
        if ($map) {
            $this->setMap($map);
        } elseif ($this->map) {
            $this->parseMap();
        }


        if ($params) {
            $this->setParams($params);
        }
        $this->init();
    }

    protected function parseMap()
    {
        $this->setParts(explode("/", trim($this->map, '/')));
    }

    public function init()
    {
    }

    /**
     * @param $uri
     * @return bool
     */
    public function match($uri)
    {
        $this->setUri($uri);

        return true;
    }

    /**
     * @param mixed $uri
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    /**
     * @param array $params
     * @return string
     */
    public function assemble($params = [])
    {
        $return = $this->getMap();

        if ($params) {
            foreach ($params as $key => $value) {
                if (stristr($return, ":" . $key) !== false) {
                    $return = str_replace(":" . $key, $value, $return);
                    unset($params[$key]);
                }
                if (array_key_exists($key, $this->params)) {
                    unset($params[$key]);
                }
            }
            if ($params) {
                $return .= "?" . http_build_query($params);
            }
        }

        // set defaults
        if ($this->params) {
            foreach ($this->params as $key => $value) {
                if (is_string($value)) {
                    $return = str_replace(":" . $key, $value, $return);
                }
            }
        }

        return $return;
    }

    /**
     * @return string
     */
    public function getMap()
    {
        return $this->map;
    }

    /**
     * @param boolean $map
     */
    public function setMap($map)
    {
        $this->map = $map;
        $this->parseMap();
    }

    /**
     * @param $params
     * @return array
     */
    public function stripEmptyParams($params)
    {
        if (is_array($params)) {
            foreach ($params as $key => $param) {
                if (empty($param)) {
                    unset($params[$key]);
                } elseif (is_array($param)) {
                    $newParams = $this->stripEmptyParams($param);
                    if (!is_array($newParams) or count($newParams) < 1) {
                        unset($params[$key]);
                    } else {
                        $params[$key] = $newParams;
                    }
                }
            }
        }

        return $params;
    }

    /**
     * @return array
     */
    public function getParts()
    {
        return $this->parts;
    }

    /**
     * @param array $parts
     */
    public function setParts($parts)
    {
        $this->parts = $parts;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param array $params
     */
    public function setParams($params = [])
    {
        if (count($params)) {
            foreach ($params as $key => $value) {
                $this->setParam($key, $value);
            }
        }
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function setParam($name, $value)
    {
        $this->params[$name] = $value;

        return $this;
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    public function getParam($key)
    {
        return $this->hasParam($key) ? $this->params[$key] : null;
    }

    /**
     * @param $key
     * @return bool
     */
    public function hasParam($key)
    {
        return isset($this->params[$key]);
    }

    /**
     * @return array
     */
    public function getMatches()
    {
        return $this->matches;
    }

    /**
     * @return array
     */
    public function getVariables()
    {
        return $this->variables;
    }

    /**
     * @param array $variables
     */
    public function setVariables($variables)
    {
        $this->variables = $variables;
    }

//    public function setModule($module)
//    {
//        $this->_module = $module;
//    }
//
//    public function setController($controller)
//    {
//        $this->_controller = $controller;
//    }
//
//    public function setAction($action)
//    {
//        $this->_action = $action;
//    }
}
