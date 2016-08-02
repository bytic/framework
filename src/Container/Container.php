<?php

namespace Nip\Container;

use ArrayAccess;
use Interop\Container\ContainerInterface;
use Nip\Container\Definition\ClassDefinition;
use Nip\Container\Definition\DefinitionInterface;
use Nip\Container\Exception\NotFoundException;

/**
 * Class Container
 * @package Nip\Container
 *
 * @inspiration https://github.com/laravel/framework/blob/2a38acf7ee2882d831a3b9a1361a710e70ffa31e/src/Illuminate/Container/Container.php
 */
class Container implements ArrayAccess, ContainerInterface
{

    /**
     * @var Definition\DefinitionInterface[]
     */
    protected $definitions = [];

    /**
     * The current globally available container (if any).
     *
     * @var static
     */
    protected static $instance;

    /**
     * The container's shared instances.
     *
     * @var array
     */
    protected $instances = [];


    public function add($id, $concrete = null, $share = false)
    {
        if (is_null($concrete)) {
            $concrete = $id;
        }

        $this->dropStaleInstances($id);

        $definition = $this->newDefinition($id, $concrete);
        if ($definition instanceof DefinitionInterface) {
            $definition->setShared($share);
            $this->definitions[$id] = $definition;
            return $definition;
        }

        // dealing with a value that cannot build a definition
        $this->instances[$id] = $concrete;
        return $concrete;
    }


    /**
     * Register a shared binding in the container.
     *
     * @param  string|array $abstract
     * @param  \Closure|string|null $concrete
     * @return void
     */
    public function singleton($abstract, $concrete = null)
    {
        $this->add($abstract, $concrete, true);
    }

    public function get($id, array $args = [])
    {
        $instance = $this->getFromThisContainer($id, $args);

        if ($instance !== false) {
            return $instance;
        }

        throw new NotFoundException(
            sprintf('Alias (%s) is not being managed by the container', $id)
        );
    }

    public function has($id)
    {
        return $this->hasInstance($id) || $this->hasDefinition($id);
    }

    public function set($id, $concrete = null)
    {
        $this->instances[$id] = $concrete;
    }

    protected function hasInstance($id)
    {
        return array_key_exists($id, $this->instances);
    }

    /**
     * Drop all of the stale instances and aliases.
     *
     * @param  string  $id
     * @return void
     */
    protected function dropStaleInstances($id)
    {
        unset($this->instances[$id]);
    }

    /**
     * @param $id
     * @param $concrete
     * @return ClassDefinition
     */
    public function newDefinition($id, $concrete)
    {
//        if (is_callable($concrete)) {
//            $concrete = new CallableDefinition($id, $concrete);
        if (is_string($concrete) && class_exists($concrete)) {
            $concrete = new ClassDefinition($id, $concrete);
        }
        // if the item is not defineable we just return the value to be stored
        // in the container as an arbitrary value/instance
        return $concrete;
    }

    /**
     * @param $id
     * @return ClassDefinition
     */
    public function getDefinition($id)
    {
        return $this->definitions[$id];
    }

    protected function hasDefinition($id)
    {
        return array_key_exists($id, $this->definitions);
    }


    protected function getFromThisContainer($id, array $args = [])
    {
        if ($this->hasInstance($id)) {
            return $this->instances[$id];
        }

        if ($this->hasDefinition($id)) {
            $definition = $this->getDefinition($id);
            $instance = $definition->build($args);
            if ($definition->isShared()) {
                $this->instances[$id] = $instance;
            }
            return $instance;
        }

        return false;
    }

    /**
     * Set the globally available instance of the container.
     *
     * @return static
     */
    public static function getInstance()
    {
        return static::$instance;
    }

    /**
     * Set the shared instance of the container.
     *
     * @param  Container $container
     * @return void
     */
    public static function setInstance(Container $container)
    {
        static::$instance = $container;
    }

    /**
     * Determine if a given offset exists.
     *
     * @param  string $key
     * @return bool
     */
    public function offsetExists($key)
    {
    }

    /**
     * Get the value at a given offset.
     *
     * @param  string $key
     * @return mixed
     */
    public function offsetGet($key)
    {
    }

    /**
     * Set the value at a given offset.
     *
     * @param  string $key
     * @param  mixed $value
     * @return void
     */
    public function offsetSet($key, $value)
    {
    }

    /**
     * Unset the value at a given offset.
     *
     * @param  string $key
     * @return void
     */
    public function offsetUnset($key)
    {
    }

    /**
     * Dynamically access container services.
     *
     * @param  string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this[$key];
    }

    /**
     * Dynamically set container services.
     *
     * @param  string $key
     * @param  mixed $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this[$key] = $value;
    }
}