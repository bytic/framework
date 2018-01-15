<?php

namespace Nip;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;

/**
 * Class Collection.
 */
class Collection implements Countable, IteratorAggregate, ArrayAccess
{
    protected $items = [];
    protected $index = 0;

    /**
     * Collection constructor.
     *
     * @param array $items
     */
    public function __construct($items = [])
    {
        if (is_array($items)) {
            $this->items = $items;
        } elseif ($items instanceof self) {
            $this->items = $items->toArray();
        }
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $return = $this->items;
        reset($return);

        return $return;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }

    /**
     * @param $index
     *
     * @return bool
     */
    public function exists($index)
    {
        return $this->offsetExists($index);
    }

    /**
     * @param mixed $index
     *
     * @return bool
     */
    public function offsetExists($index)
    {
        return in_array($index, array_keys($this->items));
    }

    /**
     * @param mixed $index
     * @param mixed $value
     */
    public function offsetSet($index, $value)
    {
        if (is_null($index)) {
            $index = $this->index++;
        }
        $this->items[$index] = $value;
    }

    /**
     * @param mixed $index
     */
    public function offsetUnset($index)
    {
        if ($this->offsetExists($index)) {
            unset($this->items[$index]);
        }
    }

    /**
     * @return mixed
     */
    public function end()
    {
        $this->index = count($this->items);

        return end($this->items);
    }

    /**
     * @return mixed
     */
    public function current()
    {
        return current($this->items);
    }

    /**
     * @return mixed
     */
    public function next()
    {
        $this->index++;

        return next($this->items);
    }

    /**
     * @param $value
     * @param bool $index
     *
     * @return $this
     */
    public function unshift($value, $index = false)
    {
        if (is_null($index)) {
            $index = $this->index++;
        }

        $this->items = array_reverse($this->items, true);
        $this->items[$index] = $value;
        $this->items = array_reverse($this->items, true);

        return $this;
    }

    /**
     * @return $this
     */
    public function clear()
    {
        $this->rewind();
        $this->items = [];

        return $this;
    }

    /**
     * @return mixed
     */
    public function rewind()
    {
        $this->index = 0;

        return reset($this->items);
    }

    /**
     * @return $this
     */
    public function ksort()
    {
        ksort($this->items);
        $this->rewind();

        return $this;
    }

    /**
     * @param $callback
     *
     * @return $this
     */
    public function usort($callback)
    {
        usort($this->items, $callback);
        $this->rewind();

        return $this;
    }

    /**
     * @param $callback
     *
     * @return $this
     */
    public function uasort($callback)
    {
        uasort($this->items, $callback);
        $this->rewind();

        return $this;
    }

    /**
     * @return $this
     */
    public function shuffle()
    {
        shuffle($this->items);
        $this->rewind();

        return $this;
    }

    /**
     * @param $key
     *
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
        $this->items = $items;
    }

    /**
     * @param $index
     *
     * @return mixed|null
     */
    public function getNth($index)
    {
        $keys = $this->keys();
        $index = $index - 1;
        if (isset($keys[$index])) {
            $key = $keys[$index];

            return $this->offsetGet($key);
        }
    }

    /**
     * @return array
     */
    public function keys()
    {
        return array_keys($this->items);
    }

    /**
     * @param mixed $index
     *
     * @return mixed|null
     */
    public function offsetGet($index)
    {
        return $this->offsetExists($index) ? $this->items[$index] : null;
    }
}
