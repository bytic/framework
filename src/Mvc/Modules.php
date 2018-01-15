<?php

namespace Nip\Mvc;

use ArrayAccess;

/**
 * Class Modules.
 */
class Modules implements ArrayAccess
{
    protected $modules = [];

    /**
     * Modules constructor.
     */
    public function __construct()
    {
        $this->init();
    }

    public function init()
    {
        $this->addModule('admin');
        $this->addModule('default');
    }

    /**
     * @param $name
     */
    public function addModule($name)
    {
        if (!$this->offsetExists($name)) {
            $this->modules[$name] = $name;
        }
    }

    /**
     * Determine if a given offset exists.
     *
     * @param string $key
     *
     * @return bool
     */
    public function offsetExists($key)
    {
        return array_key_exists($key, $this->modules);
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function hasModule($name)
    {
        return $this->offsetExists($name);
    }

    /**
     * @return array
     */
    public function getNames()
    {
        return $this->modules;
    }

    /**
     * @param $name
     *
     * @return string
     */
    public function getViewPath($name)
    {
        return $this->getModuleDirectory($name).DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR;
    }

    /**
     * @param $name
     *
     * @return string
     */
    public function getModuleDirectory($name)
    {
        return $this->getModulesBaseDirectory().$name;
    }

    /**
     * @return string
     */
    public function getModulesBaseDirectory()
    {
        return defined('MODULES_PATH') ? MODULES_PATH : '';
    }

    /**
     * Get the value at a given offset.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->modules[$key];
    }

    /**
     * Set the value at a given offset.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    public function offsetSet($key, $value)
    {
        $this->modules[$key] = $value;
    }

    /**
     * Unset the value at a given offset.
     *
     * @param string $key
     *
     * @return void
     */
    public function offsetUnset($key)
    {
        unset($this->modules[$key]);
    }
}
