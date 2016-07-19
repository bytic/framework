<?php

class Nip_Collection implements Countable, IteratorAggregate, ArrayAccess
{
    protected $_items = array();
    protected $_index = 0;

    public function __construct($items = array())
    {
        if (is_array($items)) {
            $this->_items = $items;
        } elseif ($items instanceof Nip_Collection) {
            $this->_items = $items->toArray();
        }
    }

    public function count()
    {
        return count($this->_items);
    }

    public function getIterator()
    {
        return new ArrayIterator($this->_items);
    }

    public function offsetExists($index)
    {
        return in_array($index, array_keys($this->_items));
    }

    public function exists($index)
    {
        return $this->offsetExists($index);
    }

    public function offsetGet($index)
    {
        return $this->offsetExists($index) ? $this->_items[$index] : null;
    }

    public function offsetSet($index, $value)
    {
        if (is_null($index)) {
            $index = $this->_index++;
        }
        $this->_items[$index] = $value;
    }

    public function offsetUnset($index)
    {
        if ($this->offsetExists($index)) {
            unset($this->_items[$index]);
        }
    }

    public function rewind()
    {
        $this->_index = 0;
        return reset($this->_items);
    }

    public function end()
    {
        $this->_index = count($this->_items);
        return end($this->_items);
    }

    public function current()
    {
        return current($this->_items);
    }

    public function next()
    {
        $this->_index++;
        return next($this->_items);
    }

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

    public function toArray()
    {
        $return = $this->_items;
        reset($return);
        return $return;
    }

    public function clear()
    {
        $this->rewind();
        $this->_items = array();

        return $this;
    }

    public function keys()
    {
        return array_keys($this->_items);
    }

    public function ksort()
    {
        ksort($this->_items);
        $this->rewind();
        return $this;
    }

    public function usort($callback)
    {
        usort($this->_items, $callback);
        $this->rewind();
        return $this;
    }

    public function uasort($callback)
    {
        uasort($this->_items, $callback);
        $this->rewind();
        return $this;
    }

    public function shuffle()
    {
        shuffle($this->_items);
        $this->rewind();
        return $this;
    }
}