<?php

namespace Nip\Collections\Traits;

use Traversable;

/**
 * Class OperationsTrait
 * @package Nip\Collections\Traits
 */
trait OperationsTrait
{

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
     * Returns number of items in $collection.
     *
     * @return int
     */
    public function size()
    {
        $result = 0;
        foreach ($this as $value) {
            $result++;
        }
        return $result;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return $this->count() < 1;
    }

    /**
     * @return bool
     */
    public function isNotEmpty()
    {
        return !$this->isEmpty();
    }
}
