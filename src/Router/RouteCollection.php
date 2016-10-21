<?php

namespace Nip\Router;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Nip\Router\Route\Route;

/**
 * Class RouteCollection
 * @package Nip\Router
 */
class RouteCollection implements Countable, IteratorAggregate
{

    protected $routes = [];

    /**
     * @param Route $route
     * @param null $name
     */
    public function add(Route $route, $name = null)
    {
        if ($name) {
            $route->setName($name);
        } else {
            $name = $route->getName();
        }
        $this->routes[$name] = $route;
    }

    /**
     * @param $path
     */
    public function loadFromIncludedPhp($path)
    {
        /** @noinspection PhpIncludeInspection */
        require_once $path;
    }

    /**
     * @param $route
     * @return bool
     */
    public function hasRoute($route)
    {
        $name = $route instanceof Route ? $route->getName() : $route;

        return array_key_exists($name, $this->routes);
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->routes);
    }

    /**
     * Get an iterator for the items.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->getRoutes());
    }

    /**
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * @param array $routes
     */
    public function setRoutes($routes)
    {
        $this->routes = $routes;
    }
}
