<?php

namespace Nip\Router;

use Nip\Request;
use Nip\Router\Route\AbstractRoute as Route;
use Psr\Http\Message\ServerRequestInterface;

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
     * @return null|Route\Route
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
     * @param Request|ServerRequestInterface $request
     * @return array
     */
    public function route($request)
    {
        $current = false;
        $uri = $request->path();

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
     * @param boolean $params
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
     * @return null|Route\Route
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
                    $params['controller'] = isset($parts[0]) ? $parts[0] : null;
                    $params['action'] = isset($parts[1]) ? $parts[1] : null;
                    $route = $this->getRoute($module . '.default');
                }
            }
        }

        return $route;
    }

    /**
     * @return Request
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
     * @param boolean $params
     * @return string|null
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
     * @param string $name
     * @return bool
     */
    public function hasRoute($name)
    {
        return $this->getRoutes()->has($name);
    }
}
