<?php

namespace Nip\Config;

use ArrayAccess;
use Countable;
use Iterator;

/**
 * Class Repository
 * @package Nip\Config
 */
class Config implements Countable, Iterator, ArrayAccess
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
     * Used when unsetting values during iteration to ensure we do not skip
     * the next element.
     *
     * @var bool
     */
    protected $skipNextIteration;

    /**
     * Create a new configuration repository.
     *
     * @param array $array
     * @param bool $allowModifications
     */
    public function __construct(array $array = [], $allowModifications = false)
    {
        $this->allowModifications = (bool)$allowModifications;

        foreach ($array as $key => $value) {
            $this->setDataItem($key, $value);
        }
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    protected function setDataItem($name, $value)
    {
        if (is_array($value)) {
            $value = new static($value, true);
        }
        if (null === $name) {
            $this->data[] = $value;
        } else {
            $this->data[$name] = $value;
        }

        return $this;
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
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if (strpos($key, '.') === false) {
            $value = $this->getByKey($key);
        } else {
            $value = $this->getByPath($key);
        }

        return $value === null ? $default : $value;
    }

    /**
     * @param $key
     * @return mixed|null
     */
    public function getByKey($key)
    {
        if ($this->hasByKey($key)) {
            return $this->data[$key];
        }

        return null;
    }

    /**
     * Determine if the given configuration value exists.
     *
     * @param  string $key
     * @return bool
     */
    public function hasByKey($key)
    {
        return isset($this->data[$key]);
    }

    /**
     * @param $path
     * @return string
     */
    protected function getByPath($path)
    {
        $segments = explode('.', $path);
        $value = $this;
        foreach ($segments as $segment) {
            if ($value->hasByKey($segment)) {
                $value = $value->getByKey($segment);
            } else {
                return null;
            }
        }

        return $value;
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
        if (strpos($key, '.') === false) {
            return $this->hasByKey($key);
        }

        return $this->hasByPath($key);
    }

    /**
     * @param $path
     * @return bool
     */
    public function hasByPath($path)
    {
        $segments = explode('.', $path);
        $value = $this;
        foreach ($segments as $segment) {
            if ($value->hasByKey($segment)) {
                $value = $value->getByKey($segment);
            } else {
                return false;
            }
        }

        return ($value !== null);
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
     * @param $name
     * @param $value
     * @return $this
     * @throws Exception\RuntimeException
     */
    public function set($name, $value)
    {
        if ($this->isReadOnly()) {
            throw new Exception\RuntimeException('Config is read only');
        }

        return $this->setDataItem($name, $value);
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
     * @param string $path
     * @return $this
     */
    public function mergeFile($path)
    {
        $data = Factory::fromFile($path, false);
        $config = new self($data, $this->allowModifications);
        return $this->merge($config);
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

    /**
     * count(): defined by Countable interface.
     *
     * @see    Countable::count()
     * @return int
     */
    public function count()
    {
        return count($this->data);
    }

    /**
     * current(): defined by Iterator interface.
     *
     * @see    Iterator::current()
     * @return mixed
     */
    public function current()
    {
        $this->skipNextIteration = false;

        return current($this->data);
    }

    /**
     * next(): defined by Iterator interface.
     *
     * @see    Iterator::next()
     * @return void
     */
    public function next()
    {
        if ($this->skipNextIteration) {
            $this->skipNextIteration = false;

            return;
        }
        next($this->data);
    }

    /**
     * rewind(): defined by Iterator interface.
     *
     * @see    Iterator::rewind()
     * @return void
     */
    public function rewind()
    {
        $this->skipNextIteration = false;
        reset($this->data);
    }

    /**
     * valid(): defined by Iterator interface.
     *
     * @see    Iterator::valid()
     * @return bool
     */
    public function valid()
    {
        return ($this->key() !== null);
    }

    /**
     * key(): defined by Iterator interface.
     *
     * @see    Iterator::key()
     * @return mixed
     */
    public function key()
    {
        return key($this->data);
    }
}
