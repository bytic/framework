<?php

namespace Nip\Collections;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use JsonSerializable;
use Nip\Collections\Traits\AccessMethodsTrait;
use Nip\Collections\Traits\ArrayAccessTrait;
use Nip\Collections\Traits\OperationsTrait;
use Nip\Collections\Traits\SortingTrait;
use Nip\Collections\Traits\TransformMethodsTrait;

/**
 * Class Registry
 * @package Nip
 */
class AbstractCollection implements ArrayAccess, Countable, IteratorAggregate, JsonSerializable
{
    use ArrayAccessTrait;
    use AccessMethodsTrait;
    use OperationsTrait;
    use SortingTrait;
    use TransformMethodsTrait;

    /**
     * @var array
     */
    protected $items = [];

    protected $index = 0;

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
     * @param $needle
     * @return bool
     */
    public function contains($needle)
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
