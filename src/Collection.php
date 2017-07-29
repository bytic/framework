<?php

namespace Nip;

use Nip\Collections\AbstractCollection;

/**
 * Class Collection
 * @package Nip
 */
class Collection extends AbstractCollection
{

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
        return null;
    }

    /**
     * @return array
     */
    public function keys()
    {
        return array_keys($this->items);
    }
}