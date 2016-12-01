<?php

namespace Nip;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;

/**
 * Class Collection
 * @package Nip
 */
class Collection implements Countable, IteratorAggregate, ArrayAccess
{
    protected $_items = [];
    protected $_index = 0;

    public function __construct($items = [])
    {
        if (is_array($items)) {
            $this->_items = $items;
        } elseif ($items instanceof Collection) {
            $this->_items = $items->toArray();
        }
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $return = $this->_items;
        reset($return);

        return $return;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->_items);
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->_items);
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
     * @param mixed $index
     * @return bool
     */
    public function offsetExists($index)
    {
        return in_array($index, array_keys($this->_items));
    }

    /**
     * @param mixed $index
     * @return mixed|null
     */
    public function offsetGet($index)
    {
        return $this->offsetExists($index) ? $this->_items[$index] : null;
    }

    /**
     * @param mixed $index
     * @param mixed $value
     */
    public function offsetSet($index, $value)
    {
        if (is_null($index)) {
            $index = $this->_index++;
        }
        $this->_items[$index] = $value;
    }

    /**
     * @param mixed $index
     */
    public function offsetUnset($index)
    {
        if ($this->offsetExists($index)) {
            unset($this->_items[$index]);
        }
    }

    /**
     * @return mixed
     */
    public function end()
    {
        $this->_index = count($this->_items);
        return end($this->_items);
    }

    /**
     * @return mixed
     */
    public function current()
    {
        return current($this->_items);
    }

    /**
     * @return mixed
     */
    public function next()
    {
        $this->_index++;
        return next($this->_items);
    }

    /**
     * @param $value
     * @param bool $index
     * @return $this
     */
    public function unshift($value, $index = false)
    {
        if (is_null($index)) {
            $index = $this->_index++;
        }

        $this->_items = array_reverse($this->_items, true);
        $this->_items[$index] = $value;
        $this->_items = array_reverse($this->_items, true);

        return $this;
    }

    /**
     * @return $this
     */
    public function clear()
    {
        $this->rewind();
        $this->_items = [];

        return $this;
    }

    /**
     * @return mixed
     */
    public function rewind()
    {
        $this->_index = 0;

        return reset($this->_items);
    }

    /**
     * @return array
     */
    public function keys()
    {
        return array_keys($this->_items);
    }

    /**
     * @return $this
     */
    public function ksort()
    {
        ksort($this->_items);
        $this->rewind();
        return $this;
    }

    /**
     * @param $callback
     * @return $this
     */
    public function usort($callback)
    {
        usort($this->_items, $callback);
        $this->rewind();
        return $this;
    }

    /**
     * @param $callback
     * @return $this
     */
    public function uasort($callback)
    {
        uasort($this->_items, $callback);
        $this->rewind();
        return $this;
    }

    /**
     * @return $this
     */
    public function shuffle()
    {
        shuffle($this->_items);
        $this->rewind();
        return $this;
    }

    /**
     * @param $key
     * @return $this
     */
    public function keyBy($key)
    {
        $oldItems = $this->toArray();
        $newItems = [];
        foreach ($oldItems as $item) {
            $aKey = $item->{$key};
            $newItems[$aKey] = $item;
        }
        $this->setItems($newItems);
        return $this;
    }

    /**
     * @param array $items
     */
    public function setItems($items)
    {
        $this->_items = $items;
    }
}