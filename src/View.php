<?php

namespace Nip;

/**
 * Class View
 *
 * @method Helpers\View\Breadcrumbs Breadcrumbs()
 * @method Helpers\View\Doctype Doctype()
 * @method Helpers\View\Flash Flash()
 * @method Helpers\View\FacebookMeta FacebookMeta()
 * @method Helpers\View\GoogleAnalytics GoogleAnalytics()
 * @method Helpers\View\HTML HTML()
 * @method Helpers\View\Messages Messages()
 * @method Helpers\View\Meta Meta()
 * @method Helpers\View\Paginator Paginator()
 * @method Helpers\View\Scripts Scripts()
 * @method Helpers\View\StyleSheets StyleSheets()
 * @method Helpers\View\TinyMCE TinyMCE()
 * @method Helpers\View\Url Url()
 *
 */
class View
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
        return null;
    }

    public function __set($name, $value)
    {
        return $this->set($name, $value);
    }

    public function &__get($name)
    {
        return $this->get($name);
    }

    public function __isset($name)
    {
        return isset($this->_data[$name]);
    }

    public function __unset($name)
    {
        unset($this->_data[$name]);
    }
    /**
     * @param string $name
     * @param mixed $value
     */
    public function set($name, $value)
    {
        $this->_data[$name] = $value;
    }

    /**
     * @param  string $name
     * @return mixed|null
     */
    public function get($name)
    {
        if ($this->has($name)) {
            return $this->_data[$name];
        } else {
            return null;
        }
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->_data[$name]);
    }

    /**
     * @param string $name
     * @param string $appended
     */
    public function append($name, $appended)
    {
        $value = $this->has($name) ? $this->get($name) : '';
        $value .= $appended;
        return $this->set($name, $value);
    }

    public function getHelper($name)
    {
        if (!isset($this->_helpers[$name])) {
            $this->initHelper($name);
        }

        return $this->_helpers[$name];
    }

    public function getHelperClass($name)
    {
        return '\Nip\Helpers\View\\' . $name;
    }

    public function initHelper($name)
    {
        $this->_helpers[$name] = $this->newHelper($name);
    }

    public function newHelper($name)
    {
        $class = $this->getHelperClass($name);
        $helper = new $class();
        /** @var \Nip\Helpers\View\AbstractHelper $helper */
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
        if ($this->_basePath === null) {
            $this->initBasePath();
        }
        return $this->_basePath;
    }

    public function initBasePath()
    {
        if (defined('VIEWS_PATH')) {
            $this->_basePath = VIEWS_PATH;
        }
        $this->_basePath = false;
    }

    /** @noinspection PhpInconsistentReturnPointsInspection
     *
     * @param $view
     * @param array $variables
     * @param bool $return
     * @return string|null
     */
    public function load($view, $variables = array(), $return = false)
    {
        $html = $this->getContents($view, $variables);

        if ($return === true)
            return $html;

        echo $html;
    }

    public function getContents($view, $variables = array())
    {
        extract($variables);

        $path = $this->buildPath($view);

        unset($view, $variables);
        ob_start();
        /** @noinspection PhpIncludeInspection */
        include($path);
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
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

    public function isBlock($block = 'default')
    {
        return empty($this->_blocks[$block]) ? false : true;
    }

    /**
     * Assigns variables in bulk in the current scope
     *
     * @param array $array
     * @return $this
     */
    public function assign($array = array())
    {
        foreach ($array as $key => $value) {
            if (is_string($key)) {
                $this->set($key, $value);
            }
        }
        return $this;
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
            $caller = $backtrace[3]['file'];

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
}