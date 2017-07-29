<?php

namespace Nip\Collections;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Nip\Collections\Traits\ArrayAccessTrait;

/**
 * Class Registry
 * @package Nip
 */
class AbstractCollection implements ArrayAccess, Countable, IteratorAggregate, JsonSerializable
{
    use ArrayAccessTrait;


    /**
     * Collection constructor.
     * @param array $items
     */
    public function __construct($items = [])
    {
        if (is_array($items)) {
            $this->items = $items;
        } elseif ($items instanceof AbstractCollection) {
            $this->items = $items->toArray();
        }
    }

    /**
     * @var array
     */
    protected $items = [];

    protected $index = 0;

    /**
     * @return boolean
     * @param string $key
     */
    public function has($key)
    {
        return $this->offsetExists($key);
    }

    /**
     * @param $index
     * @return bool
     */
    public function exists($index)
    {
        return $this->offsetExists($index);
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

    /**
     * @param $needle
     * @return bool
     */
    function contains($needle)
    {
        foreach ($this as $key => $value) {
            if ($value === $needle) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }
}