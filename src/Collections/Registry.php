<?php

namespace Nip\Collections;

use ArrayAccess;

/**
 * Class Registry
 * @package Nip
 */
class Registry implements ArrayAccess
{
    /**
     * @var array
     */
    protected $items = [];

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
     * @return boolean
     * @param string $id
     */
    public function has($id)
    {
        return isset($this->items[$id]);
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
     * @return mixed
     * @param string $id
     */
    public function get($id)
    {
        return $this->items[$id];
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
     * @param string $id
     * @param mixed $value
     */
    public function set($id, $value)
    {
        $this->items[$id] = $value;
    }

    /**
     * Unset a configuration option.
     *
     * @param  string $key
     * @return void
     */
    public function offsetUnset($key)
    {
        $this->unset($key);
    }

    /**
     * @param string $id
     */
    public function unset($id)
    {
        unset($this->items[$id]);
    }
}