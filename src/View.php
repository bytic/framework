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
 * @method Helpers\View\Stylesheets StyleSheets()
 * @method Helpers\View\TinyMCE TinyMCE()
 * @method Helpers\View\Url Url()
 *
 */
class View
{
    protected $request = null;

    protected $helpers = [];

    protected $data = [];
    protected $blocks = [];
    protected $basePath = null;

    /**
     * @param $name
     * @param $arguments
     * @return mixed|null
     */
    public function __call($name, $arguments)
    {
        if ($name === ucfirst($name)) {
            return $this->getHelper($name);
        } else {
            trigger_error("Call to undefined method $name", E_USER_ERROR);
        }
        return null;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getHelper($name)
    {
        if (!isset($this->helpers[$name])) {
            $this->initHelper($name);
        }

        return $this->helpers[$name];
    }

    /**
     * @param $name
     */
    public function initHelper($name)
    {
        $this->helpers[$name] = $this->newHelper($name);
    }

    /**
     * @param $name
     * @return Helpers\View\AbstractHelper
     */
    public function newHelper($name)
    {
        $class = $this->getHelperClass($name);
        $helper = new $class();
        /** @var \Nip\Helpers\View\AbstractHelper $helper */
        $helper->setView($this);

        return $helper;
    }

    /**
     * @param $name
     * @return string
     */
    public function getHelperClass($name)
    {
        return '\Nip\Helpers\View\\' . $name;
    }

    /**
     * @param $name
     * @return mixed|null
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * @param $name
     * @param $value
     * @return View
     */
    public function __set($name, $value)
    {
        return $this->set($name, $value);
    }

    /**
     * @param  string $name
     * @return mixed|null
     */
    public function get($name)
    {
        if ($this->has($name)) {
            return $this->data[$name];
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
        return isset($this->data[$name]);
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function set($name, $value)
    {
        $this->data[$name] = $value;
        return $this;
    }

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    /**
     * @param $name
     */
    public function __unset($name)
    {
        unset($this->data[$name]);
    }

    /**
     * @param string $name
     * @param string $appended
     * @return View
     */
    public function append($name, $appended)
    {
        $value = $this->has($name) ? $this->get($name) : '';
        $value .= $appended;
        return $this->set($name, $value);
    }

    /**
     * @param $name
     * @param $block
     */
    public function setBlock($name, $block)
    {
        $this->blocks[$name] = $block;
    }

    /**
     * @param $view
     * @return bool
     */
    public function existPath($view)
    {
        return is_file($this->buildPath($view));
    }

    /**
     * Builds path for including
     * If $view starts with / the path will be relative to the root of the views folder.
     * Otherwise to caller file location.
     *
     * @param string $view
     * @return string
     */
    protected function buildPath($view)
    {
        if ($view[0] == '/') {
            return $this->getBasePath() . ltrim($view, "/") . '.php';
        } else {
            $backtrace = debug_backtrace();
            $caller = $backtrace[3]['file'];

            return dirname($caller) . "/" . $view . ".php";
        }
    }

    /**
     * @return string
     */
    public function getBasePath()
    {
        if ($this->basePath === null) {
            $this->initBasePath();
        }

        return $this->basePath;
    }

    /**
     * @param string $path
     * @return $this
     */
    public function setBasePath($path)
    {
        $path = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $this->basePath = $path;

        return $this;
    }

    protected function initBasePath()
    {
        $this->setBasePath($this->generateBasePath());
    }

    /**
     * @return string
     */
    protected function generateBasePath()
    {
        if (defined('VIEWS_PATH')) {
            return VIEWS_PATH;
        }
        return false;
    }

    /**
     * @param string $block
     */
    public function render($block = 'default')
    {
        if (!empty($this->blocks[$block])) {
            $this->load("/" . $this->blocks[$block]);
        } else {
            trigger_error("No $block block", E_USER_ERROR);
        }
    }

    /** @noinspection PhpInconsistentReturnPointsInspection
     *
     * @param $view
     * @param array $variables
     * @param bool $return
     * @return string|null
     */
    public function load($view, $variables = [], $return = false)
    {
        $html = $this->getContents($view, $variables);

        if ($return === true) {
            return $html;
        }

        echo $html;
    }

    /**
     * @param $view
     * @param array $variables
     * @return string
     */
    public function getContents($view, $variables = [])
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

    /**
     * @param string $block
     * @return bool
     */
    public function isBlock($block = 'default')
    {
        return empty($this->blocks[$block]) ? false : true;
    }

    /**
     * Assigns variables in bulk in the current scope
     *
     * @param array $array
     * @return $this
     */
    public function assign($array = [])
    {
        foreach ($array as $key => $value) {
            if (is_string($key)) {
                $this->set($key, $value);
            }
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param mixed $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }
}
