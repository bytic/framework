<?php

namespace Nip\Container;

use Interop\Container\ContainerInterface;

class Container implements ArrayAccess, ContainerInterface
{

    /**
     * The container's bindings.
     *
     * @var array
     */
    protected $bindings = [];

    /**
     * The container's shared instances.
     *
     * @var array
     */
    protected $instances = [];


    public function get($id)
    {
        return $this->instances[$id];
    }

    public function has($id)
    {
    }

    public function add($alias, $concrete = null, $share = false)
    {
        $this->instances[$alias] = $concrete;
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