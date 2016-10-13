<?php

namespace Nip\Config;

use ArrayAccess;

/**
 * Class Repository
 * @package Nip\Config
 */
class Repository implements ArrayAccess
{

    /**
     * All of the configuration items.
     *
     * @var array
     */
    protected $items = [];

    /**
     * Create a new configuration repository.
     *
     * @param  array $items
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * @param $name
     * @return string
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * @param string $key
     * @return string
     */
    public function get($key)
    {
        return $this->items[$key];
    }

    /**
     * @param $key
     * @return bool
     */
    public function __isset($key)
    {
        return $this->has($key);
    }

    /**
     * Determine if the given configuration value exists.
     *
     * @param  string $key
     * @return bool
     */
    public function has($key)
    {
        return isset($this->items[$key]);
    }

    /**
     * Determine if the given configuration option exists.
     *
     * @param  string $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return $this->has($key);
    }

    /**
     * Get a configuration option.
     *
     * @param  string $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * Set a configuration option.
     *
     * @param  string $key
     * @param  mixed $value
     * @return void
     */
    public function offsetSet($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * @param $name
     * @param $value
     */
    public function set($name, $value)
    {
        $this->items[$name] = $value;
    }

    /**
     * Unset a configuration option.
     *
     * @param  string $key
     * @return void
     */
    public function offsetUnset($key)
    {
        $this->set($key, null);
    }
}
