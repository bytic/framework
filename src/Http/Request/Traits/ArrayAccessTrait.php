<?php

namespace Nip\Http\Request\Traits;

/**
 * Class ArrayAccessTrait
 * @package Nip\Http\Request\Traits
 */
trait ArrayAccessTrait
{
    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return !empty($this->get($offset));
    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->attributes->set($offset, $value);
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        $this->attributes->remove($offset);
    }
}
