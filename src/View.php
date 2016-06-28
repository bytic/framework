<?php

class Nip_View
{
    protected $_request = null;

    protected $_helpers = array();

    protected $_data = array();
    protected $_blocks = array();
    protected $_basePath = null;

    public function __construct()
    {
    }

    public function __call($name, $arguments)
    {
        if ($name === ucfirst($name)) {
            return $this->getHelper($name);
        } else {
            trigger_error("Call to undefined method $name", E_USER_ERROR);
        }
    }

    public function __set($name, $value)
    {
        $this->_data[$name] = $value;
    }

    public function &__get($name)
    {
        if (isset($this->_data[$name])) {
            return $this->_data[$name];
        } else {
            return null;
        }
    }

    public function __isset($name)
    {
        return isset($this->_data[$name]);
    }

    public function __unset($name)
    {
        unset($this->_data[$name]);
    }

    public function getHelper($name)
    {
        if (!isset($this->_helpers[$name])) {
            $this->_helpers[$name] = $this->initHelper($name);
        }

        return $this->_helpers[$name];
    }

    public function getHelperClass($name)
    {
        return 'Nip_Helper_View_' . $name;

    }

    public function initHelper($name)
    {
        $class = $this->getHelperClass($name);
        $helper = new $class();
        $helper->setView($this);
        return $helper;
    }

    public function setBlock($name, $block)
    {
        $this->_blocks[$name] = $block;
    }

    public function setBasePath($path)
    {
        $this->_basePath = $path;
        return $this;
    }

    /**
     * @return string
     */
    public function getBasePath()
    {
        if ($this->_basePath === null && defined('VIEWS_PATH')) {
            $this->_basePath = VIEWS_PATH;
        }
        return $this->_basePath;
    }

    public function load($view, $variables = array(), $return = false)
    {
        extract($variables);

        $path = $this->buildPath($view);

        if ($return === true) {
            ob_start();
            include($path);
            $html = ob_get_contents();
            ob_end_clean();

            return $html;
        } else {
            unset($view, $variables, $return);
            include($path);
        }
    }

    public function existPath($view)
    {
        return is_file($this->buildPath($view));
    }

    public function render($block = 'default')
    {
        if (!empty($this->_blocks[$block])) {
            $this->load("/" . $this->_blocks[$block]);
        } else {
            trigger_error("No $block block", E_USER_ERROR);
        }
    }

    /**
     * Assigns variables in bulk in the current scope
     *
     * @param array $array
     */
    public function assign($input = array())
    {
        foreach ($input as $key => $value) {
            if (is_string($key)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * Builds path for including
     * If $view starts with / the path will be relative to the root of the views folder. Otherwise to caller file location.
     *
     * @param string $view
     * @return string
     */
    protected function buildPath($view)
    {
        if ($view[0] == '/') {
            return $this->_basePath . ltrim($view, "/") . '.php';
        } else {
            $backtrace = debug_backtrace();
            $caller = $backtrace[2]['file'];

            return dirname($caller) . "/" . $view . ".php";
        }
    }

    /**
     * @return mixed
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * @param mixed $request
     */
    public function setRequest($request)
    {
        $this->_request = $request;
    }

    /**
     * Singleton
     *
     * @return Nip_View
     */
    public static function instance()
    {
        static $instance;
        if (!($instance instanceof self)) {
            $instance = new self();
        }
        return $instance;
    }
}