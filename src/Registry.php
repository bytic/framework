<?php

class Nip_Registry
{
    protected $_registry;

    /**
     * Singleton.
     *
     * @return self
     */
    public static function instance()
    {
        static $instance;
        if (!($instance instanceof self)) {
            $instance = new self();
        }

        return $instance;
    }

    /**
     * @param string $id
     *
     * @return bool
     */
    public function exists($id)
    {
        return isset($this->_registry[$id]);
    }

    /**
     * @param string $id
     */
    public function delete($id)
    {
        unset($this->_registry[$id]);
    }

    /**
     * @param string $id
     *
     * @return mixed
     */
    public function get($id)
    {
        return $this->_registry[$id];
    }

    /**
     * @param string $id
     * @param mixed  $value
     */
    public function set($id, $value)
    {
        $this->_registry[$id] = $value;
    }
}
