<?php

namespace Nip\Utility\Traits;

trait SingletonTrait
{

    protected static $instance;

    /**
     * Singleton
     *
     * @return self
     */
    final public static function instance()
    {
        return isset(static::$instance) ? static::$instance : static::$instance = new static;
    }
}