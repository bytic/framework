<?php

namespace Nip\Utility\Traits;

/**
 * Class DynamicPropertiesTrait
 * @package Nip\Utility\Traits
 */
trait DynamicPropertiesTrait
{
    protected $properties;

    /**
     * @param $name
     * @return mixed
     */
    public function &__get($name)
    {
        if (!$this->__isset($name)) {
            $this->properties[$name] = null;
        }
        return $this->properties[$name];
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->properties[$name] = $value;
    }

    /**
     * @param $key
     * @return bool
     */
    public function __isset($key)
    {
        return array_key_exists($key, $this->properties);
    }

    /**
     * Get an attribute from the container.
     *
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if (isset($this->{$key})) {
            return $this->{$key};
        }

        return value($default);
    }

    /**
     * @param $name
     */
    public function __unset($name)
    {
        unset($this->properties[$name]);
    }
}
