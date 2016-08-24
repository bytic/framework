<?php

class Nip_Config
{

    protected $_data;

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

    public function parse($filename)
    {
        $config = parse_ini_file($filename, true);
        foreach ($config as $key => $value) {
            if (is_array($value)) {
                if (!isset($this->_data[$key])) {
                    $this->_data[$key] = new stdClass;
                }
                foreach ($value as $subKey => $subValue) {
                    $this->_data[$key]->$subKey = $subValue;
                }
            } else {
                $this->_data[$key] = $value;
            }
        }
        return $this;
    }

    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * @param string $name
     * @return stdClass|string
     */
    public function get($name)
    {
        return $this->_data[$name];
    }

    public function __isset($name)
    {
        return isset($this->_data[$name]);
    }

    public function set($name, $value)
    {
        $this->_data[$name] = $value;
    }
}