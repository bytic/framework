<?php

namespace Nip\Config;

use ArrayAccess;

/**
 * Class Repository
 * @package Nip\Config
 */
class Config implements ArrayAccess
{
    /**
     * Whether modifications to configuration data are allowed.
     *
     * @var bool
     */
    protected $allowModifications;

    /**
     * All of the configuration items.
     *
     * @var array
     */
    protected $data = [];

    /**
     * Create a new configuration repository.
     *
     * @param array $array
     * @param bool $allowModifications
     */
    public function __construct(array $array = [], $allowModifications = false)
    {
        foreach ($array as $key => $value) {
            $this->set($key, $value);
        }

        $this->allowModifications = (bool)$allowModifications;
    }

    /**
     * @param $name
     * @param $value
     * @throws Exception\RuntimeException
     */
    public function set($name, $value)
    {
        if ($this->isReadOnly()) {
            throw new Exception\RuntimeException('Config is read only');
        } else {
            if (is_array($value)) {
                $value = new static($value, true);
            }
            if (null === $name) {
                $this->data[] = $value;
            } else {
                $this->data[$name] = $value;
            }
        }
    }

    /**
     * Returns whether this Config object is read only or not.
     *
     * @return bool
     */
    public function isReadOnly()
    {
        return !$this->allowModifications;
    }

    /**
     * @param $name
     * @return string
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * Retrieve a value and return $default if there is no element set.
     *
     * @param  string $name
     * @param  mixed $default
     * @return mixed
     */
    public function get($name, $default = null)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        return $default;
    }

    /**
     * @param $key
     * @return bool
     */
    public function __isset($key)
    {
        return $this->has($key);
    }

    /**
     * Determine if the given configuration value exists.
     *
     * @param  string $key
     * @return bool
     */
    public function has($key)
    {
        return isset($this->data[$key]);
    }

    /**
     * Determine if the given configuration option exists.
     *
     * @param  string $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return $this->has($key);
    }

    /**
     * Get a configuration option.
     *
     * @param  string $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * Set a configuration option.
     *
     * @param  string $key
     * @param  mixed $value
     * @return void
     */
    public function offsetSet($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * Unset a configuration option.
     *
     * @param  string $key
     * @return void
     */
    public function offsetUnset($key)
    {
        $this->set($key, null);
    }

    /**
     * Merge another Config with this one.
     *
     * For duplicate keys, the following will be performed:
     * - Nested Configs will be recursively merged.
     * - Items in $merge with INTEGER keys will be appended.
     * - Items in $merge with STRING keys will overwrite current values.
     *
     * @param  self $merge
     * @return $this
     */
    public function merge(self $merge)
    {
        /** @var self $value */
        foreach ($merge as $key => $value) {
            if (array_key_exists($key, $this->data)) {
                if (is_int($key)) {
                    $this->data[] = $value;
                } elseif ($value instanceof self && $this->data[$key] instanceof self) {
                    $this->data[$key]->merge($value);
                } else {
                    if ($value instanceof self) {
                        $this->data[$key] = new static($value->toArray(), $this->allowModifications);
                    } else {
                        $this->data[$key] = $value;
                    }
                }
            } else {
                if ($value instanceof self) {
                    $this->data[$key] = new static($value->toArray(), $this->allowModifications);
                } else {
                    $this->data[$key] = $value;
                }
            }
        }

        return $this;
    }

    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public function toArray()
    {
        $array = [];
        $data = $this->data;
        /** @var self $value */
        foreach ($data as $key => $value) {
            if ($value instanceof self) {
                $array[$key] = $value->toArray();
            } else {
                $array[$key] = $value;
            }
        }

        return $array;
    }
}
