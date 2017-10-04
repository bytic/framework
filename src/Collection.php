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
}
