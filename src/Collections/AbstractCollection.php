<?php

namespace Nip\Collections;

/**
 * Class Registry
 * @package Nip
 */
class AbstractCollection
{

    /**
     * @var array
     */
    protected $items = [];

    /**
     * @return boolean
     * @param string $key
     */
    public function has($key)
    {
        return array_key_exists($key, $this->items);
    }

    /**
     * Returns a parameter by name.
     *
     * @param string $key The key
     * @param mixed $default The default value if the parameter key does not exist
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return array_key_exists($key, $this->items) ? $this->items[$key] : $default;
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
     * @param string $id
     */
    public function unset($id)
    {
        unset($this->items[$id]);
    }


    /**
     * Returns the parameters.
     *
     * @return array An array of parameters
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * Returns the parameter keys.
     *
     * @return array An array of parameter keys
     */
    public function keys()
    {
        return array_keys($this->items);
    }

    /**
     * Returns the number of parameters.
     *
     * @return int The number of parameters
     */
    public function count()
    {
        return count($this->items);
    }
}