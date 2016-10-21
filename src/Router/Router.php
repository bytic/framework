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
        return $this->routes[$name];
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
     * @return mixed|string
     */
    public function assemble($name, $params = [])
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
        return array_key_exists($name, $this->routes);
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
}
