<?php

namespace Nip\Collections\Traits;

/**
 * Class AccessMethodsTrait
 * @package Nip\Collections\Traits
 */
trait AccessMethodsTrait
{


    /**
     * @param array $items
     */
    public function setItems($items)
    {
        $this->items = $items;
    }

    /**
     * {@inheritDoc}
     * @param \Nip\Records\AbstractModels\Record $element
     */
    public function add($element, $key = null)
    {
        if ($key == null) {
            $this->items[] = $element;
            return;
        }
        $this->set($key, $element);
    }

    /**
     * @param string $id
     * @param mixed $value
     */
    public function set($id, $value)
    {
        $this->items[$id] = $value;
    }


    /**
     * Returns a parameter by name.
     *
     * @param string $key The key
     * @param mixed $default The default value if the parameter key does not exist
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return array_key_exists($key, $this->items) ? $this->items[$key] : $default;
    }

    /**
     * @return boolean
     * @param string $key
     */
    public function has($key)
    {
        return isset($this->items[$key]) || array_key_exists($key, $this->items);
    }

    /**
     * @param $key
     * @return bool
     * @deprecated Use ->has($key) instead
     */
    public function exists($key)
    {
        return $this->has($key);
    }


    /**
     * Returns the parameters.
     *
     * @return array An array of parameters
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * Returns the parameter keys.
     *
     * @return array An array of parameter keys
     */
    public function keys()
    {
        return array_keys($this->items);
    }

    /**
     * Returns the parameter values.
     *
     * @return array An array of parameter values
     */
    public function values()
    {
        return array_values($this->items);
    }


    /**
     * @param string $key
     * @return null
     */
    public function unset($key)
    {
        if (!isset($this->items[$key]) && !array_key_exists($key, $this->items)) {
            return null;
        }
        $removed = $this->items[$key];
        unset($this->items[$key]);
        return $removed;
    }
}
