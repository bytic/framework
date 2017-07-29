<?php

namespace Nip\Collections\Traits;

/**
 * Class SortingTrait
 * @package Nip\Collections\Traits
 */
trait SortingTrait
{
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
}
