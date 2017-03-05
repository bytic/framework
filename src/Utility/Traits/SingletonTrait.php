<?php

namespace Nip\Utility\Traits;

trait SingletonTrait
{

    /**
     * Singleton
     *
     * @return self
     */
    static public function instance()
    {
        static $instance;
        if (!($instance instanceof self)) {
            $instance = new self();
        }
        return $instance;
    }
}