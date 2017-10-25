<?php

namespace Nip\Router;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Nip\Router\Route\Route;

/**
 * Class RouteCollection
 * @package Nip\Router
 */
class RouteCollection implements Countable, IteratorAggregate, ArrayAccess
{
    protected $routes = [];

    /**
     * @param $path
     */
    public function loadFromIncludedPhp($path)
    {
        /** @noinspection PhpIncludeInspection */
        require_once $path;
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

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * @param $route
     * @return bool
     */
    public function has($route)
    {
        $name = $route instanceof Route ? $route->getName() : $route;

        return array_key_exists($name, $this->routes);
    }

    /**
     * @param mixed $offset
     * @return Route|null
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @param $route
     * @return null|Route
     */
    public function get($route)
    {
        $name = $route instanceof Route ? $route->getName() : $route;
        if ($this->has($name)) {
            return $this->routes[$name];
        }

        return null;
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->add($offset, $value);
    }

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
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->routes[$offset]);
    }
}
