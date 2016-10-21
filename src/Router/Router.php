<?php

namespace Nip\Router;

use Nip\Request;
use Nip\Router\Route\AbstractRoute as Route;

/**
 * Class Router
 * @package Nip\Router
 */
class Router
{

    /**
     * @var \Nip\Request
     */
    protected $request;


    /**
     * @var Route
     */
    protected $route;

    /**
     * @var RouteCollection|Route[]
     */
    protected $routes = null;

    /**
     * @param $name
     * @return bool
     */
    public function connected($name)
    {
        return ($this->getRoute($name) instanceof Route);
    }

    /**
     * @param $name
     * @return Route
     */
    public function getRoute($name)
    {
        return $this->getRoutes()->get($name);
    }

    /**
     * @return RouteCollection
     */
    public function getRoutes()
    {
        if ($this->routes === null) {
            $this->initRoutes();
        }

        return $this->routes;
    }

    protected function initRoutes()
    {
        $this->routes = $this->newRoutesCollection();
    }

    /**
     * @return RouteCollection
     */
    protected function newRoutesCollection()
    {
        return new RouteCollection();
    }

    /**
     * @param Request $request
     * @return array
     */
    public function route($request)
    {
        $current = false;
        $uri = $request->getHttp()->getPathInfo();

        foreach ($this->routes as $name => $route) {
            $route->setRequest($request);
            if ($route->match($uri)) {
                $current = $route;
                break;
            }
        }

        if ($current instanceof Route) {
            $this->setCurrent($current);
            $current->populateRequest();

            return $current->getParams() + $current->getMatches();
        } else {
            return [];
        }
    }

    /**
     * @param Route $route
     * @return $this
     */
    public function setCurrent($route)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * @param $name
     * @param array $params
     * @return string
     */
    public function assembleFull($name, $params = [])
    {
        $route = $this->getDefaultRoute($name, $params);
        if ($route) {
            $route->setRequest($this->getRequest());
            return $route->assembleFull($params);
        }

        trigger_error("Route \"$name\" not connected", E_USER_ERROR);

        return null;
    }

    /**
     * @param $name
     * @param array $params
     * @return Route
     */
    public function getDefaultRoute($name, &$params = [])
    {
        $route = $this->getRoute($name);
        if (!$route) {
            $parts = explode(".", $name);
            $count = count($parts);
            if ($count <= 3) {
                if (in_array(reset($parts), app('mvc.modules')->getNames())) {
                    $module = array_shift($parts);
                    list($params['controller'], $params['action']) = $parts;
                    $route = $this->getRoute($module.'.default');
                }
            }
        }

        return $route;
    }

    /**
     * @return mixed
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param mixed $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * @param $name
     * @param array $params
     * @return mixed|string
     */
    public function assemble($name, $params = [])
    {
        $route = $this->getDefaultRoute($name, $params);

        if ($route) {
            $route->setRequest($this->getRequest());
            return $route->assemble($params);
        }

        trigger_error("Route \"$name\" not connected", E_USER_ERROR);
        return null;
    }

    /**
     * @return Route
     */
    public function getCurrent()
    {
        return $this->route;
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasRoute($name)
    {
        return $this->getRoutes()->has($name);
    }
}
