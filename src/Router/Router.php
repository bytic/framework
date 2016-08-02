<?php

namespace Nip\Router;

use Nip\Request;
use Nip\Router\Route\AbstractRoute as Route;

use Nip_Profiler as Profiler;

class Router
{

    /**
     * @var \Nip\Request
     */
    protected $_request;


    /**
     * @var Route
     */
    protected $_route;

    /**
     * @var Route[]
     */
    protected $_routes = [];

    /**
     * @param Route $route
     * @param $name
     */
    public function connect($route, $name)
    {
        $route->setRequest($this->getRequest());
        $this->_routes[$name] = $route;
    }

    public function connected($name)
    {
        return ($this->getRoute($name) instanceof Route);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function route($request)
    {
        $this->_route = false;
        $uri = $request->getHttp()->getPathInfo();

        foreach ($this->_routes as $name => $route) {
            $route->setRequest($request);
            Profiler::instance()->start('route [' . $name . '] [' . $uri . ']');
            if ($route->match($uri)) {
                $this->_route = $route;
                Profiler::instance()->end('route [' . $name . '] [' . $uri . ']');
                break;
            }

            Profiler::instance()->end('route [' . $name . '] [' . $uri . ']');
        }

        if ($this->_route) {
            $this->_route->populateRequest();
            return $this->_route->getParams() + $this->_route->getMatches();
        } else {
            return array();
        }
    }

    public function assemble($name, $params = array())
    {
        $route = $this->getRoute($name);
        if (!$route) {
            $parts = explode(".", $name);
            if (count($parts) <= 2) {
                list($params['controller'], $params['action']) = $parts;
                $route = $this->getRoute('default');
            }
        }

        if ($route) {
            return $route->assemble($params);
        }

        trigger_error("Route \"$name\" not connected", E_USER_ERROR);
    }

    public function getCurrent()
    {
        return $this->_route;
    }

    public function getRoute($name)
    {
        return $this->_routes[$name];
    }

    public function hasRoute($name)
    {
        return array_key_exists($name, $this->_routes);
    }

    public function getAll()
    {
        return $this->_routes;
    }

    /**
     * @return mixed
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * @param mixed $request
     */
    public function setRequest($request)
    {
        $this->_request = $request;
    }
}