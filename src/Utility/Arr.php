<?php

namespace Nip\Utility;

class Arr
{

    /**
     * Filter the array using the given callback.
     *
     * @param  array $array
     * @param  callable $callback
     * @return array
     */
    public static function where($array, callable $callback)
    {
        return array_filter($array, $callback, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * If the given value is not an array, wrap it in one.
     *
     * @param  mixed $value
     * @return array
     */
    public static function wrap($value)
    {
        return !is_array($value) ? [$value] : $value;
    }
}
