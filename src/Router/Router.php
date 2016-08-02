<?php

namespace Nip\Router;

class Router
{

    /**
     * @var \Nip\Request
     */
    protected $_request;

    protected $_route;
    protected $_routes;

    public function connect($route, $name)
    {
        $route->setRequest($this->getRequest());
        $this->_routes[$name] = $route;
    }

    public function connected($name)
    {
        return ($this->getRoute($name) instanceof \Nip\Router\Route\RouteAbstract);
    }

    public function route($request)
    {
        $this->_route = false;
        $uri = $request->getHTTP()->getPathInfo();

        foreach ($this->_routes as $name => $route) {
            $route->setRequest($request);
            Nip_Profiler::instance()->start('route [' . $name . '] [' . $uri . ']');
            if ($route->match($uri)) {
                $this->_route = $route;
                Nip_Profiler::instance()->end('route [' . $name . '] [' . $uri . ']');
                break;
            }

            Nip_Profiler::instance()->end('route [' . $name . '] [' . $uri . ']');
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