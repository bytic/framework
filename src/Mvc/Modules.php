<?php

namespace Nip\Mvc;

use ArrayAccess;

class Modules implements ArrayAccess
{

    protected $modules = array('admin', 'default');

    public function __construct()
    {
        $this->init();
    }

    public function init()
    {
    }

    public function hasModule($name)
    {
        return $this->offsetExists($name);
    }

    public function addModule($name)
    {
        $this[] = $name;
    }

    public function getNames()
    {
        return $this->modules;
    }

    public function getViewPath($name)
    {
        return MODULES_PATH . $name . '/views/';
    }

    /**
     * Determine if a given offset exists.
     *
     * @param  string $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return array_key_exists($key, $this->modules);
    }

    /**
     * Get the value at a given offset.
     *
     * @param  string $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->modules[$key];
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
        $this->modules[$key] = $value;
    }

    /**
     * Unset the value at a given offset.
     *
     * @param  string $key
     * @return void
     */
    public function offsetUnset($key)
    {
        unset($this->modules[$key]);
    }
}