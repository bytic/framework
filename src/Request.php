<?php

namespace Nip;

use Nip\Http\Request\Http;

/**
 * Class Request
 * @package Nip
 */
class Request extends \Symfony\Component\HttpFoundation\Request implements \ArrayAccess
{

    /**
     * Has the action been dispatched?
     * @var boolean
     */
    protected $dispatched = false;
    /**
     * Module
     * @var string
     */
    protected $module;
    /**
     * Module key for retrieving module from params
     * @var string
     */
    protected $moduleKey = 'module';
    /**
     * Controller
     * @var string
     */
    protected $controller;
    /**
     * Controller key for retrieving controller from params
     * @var string
     */
    protected $controllerKey = 'controller';
    /**
     * Action
     * @var string
     */
    protected $action;
    /**
     * Action key for retrieving action from params
     * @var string
     */
    protected $actionKey = 'action';

    /**
     * @var Http
     */
    protected $http;


    /**
     * Singleton
     *
     * @param null $newInstance
     * @return static
     */
    public static function instance($newInstance = null)
    {
        static $instance;

        if ($newInstance instanceof self) {
            $instance = $newInstance;
        }

        if (!($instance instanceof self)) {
            $instance = new self();
        }

        return $instance;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * Set an action parameter
     *
     * A $value of null will unset the $key if it exists
     *
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function setAttribute($key, $value)
    {
        $this->attributes->set($key, $value);

        return $this;
    }

    /**
     * Retrieve the action key
     *
     * @return string
     */
    public function getActionKey()
    {
        return $this->actionKey;
    }

    /**
     * Set the action key
     *
     * @param string $key
     * @return self
     */
    public function setActionKey($key)
    {
        $this->actionKey = (string)$key;

        return $this;
    }

    /**
     * @return string
     */
    public function getMCA()
    {
        return $this->getModuleName() . '.' . $this->getControllerName() . '.' . $this->getActionName();
    }

    /**
     * Retrieve the module name
     * @return string
     */
    public function getModuleName()
    {
        if (null === $this->module) {
            $this->module = $this->attributes->get($this->getModuleKey());
        }

        return $this->module;
    }

    /**
     * Retrieve the module key
     *
     * @return string
     */
    public function getModuleKey()
    {
        return $this->moduleKey;
    }

    /**
     * Set the module key
     *
     * @param string $key
     * @return $this
     */
    public function setModuleKey($key)
    {
        $this->moduleKey = (string)$key;

        return $this;
    }

    /**
     * Retrieve the controller name
     * @return string
     */
    public function getControllerName()
    {
        if ($this->controller === null) {
            $this->initControllerName();
        }

        return $this->controller;
    }

    public function initControllerName()
    {
        $this->controller = $this->attributes->get($this->getControllerKey());
    }

    /**
     * Retrieve the controller key
     *
     * @return string
     */
    public function getControllerKey()
    {
        return $this->controllerKey;
    }

    /**
     * Set the controller key
     *
     * @param string $key
     * @return self
     */
    public function setControllerKey($key)
    {
        $this->controllerKey = (string)$key;

        return $this;
    }

    /**
     * Retrieve the action name
     *
     * @return string
     */
    public function getActionName()
    {
        if (null === $this->action) {
            $this->setActionName($this->getActionDefault());
        }

        return $this->action;
    }

    /**
     * Set the action name
     * @param string $value
     * @return self
     */
    public function setActionName($value)
    {
        if ($value) {
            $this->action = $value;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getActionDefault()
    {
        return 'index';
    }

    /**
     * @param array $params
     * @return $this
     */
    public function setParams(array $params)
    {
        foreach ($params as $param => $value) {
            $this->{$param} = $value;
        }

        return $this;
    }

    /**
     * Returns Http object
     * @return Http
     */
    public function getHttp()
    {
        if (!$this->http) {
            $this->http = new Http();
            $this->http->setRequest($this);
        }

        return $this->http;
    }

    /**
     * @return bool
     */
    public function isCLI()
    {
        if (defined('STDIN')) {
            return true;
        }

        if (php_sapi_name() === 'cli') {
            return true;
        }

        if (array_key_exists('SHELL', $_ENV)) {
            return true;
        }

        if (empty($_SERVER['REMOTE_ADDR']) and !isset($_SERVER['HTTP_USER_AGENT']) and count($_SERVER['argv']) > 0) {
            return true;
        }

        if (!array_key_exists('REQUEST_METHOD', $_SERVER)) {
            return true;
        }

        return false;
    }

    /**
     * Checks if requested URI matches $url parameter,
     * and redirects to $url if not
     *
     * @param string $url
     * @param int $code
     */
    public function checkURL($url, $code = 302)
    {
        $components = parse_url($url);
        $request = parse_url($_SERVER['REQUEST_URI']);

        if ($components['path'] != $request['path']) {
            $redirect = $url . ($request['query'] ? '?' . $request['query'] : '');

            header("Location: " . $redirect, true, $code);
            exit();
        }
    }

    /**
     * @param $url
     */
    public function redirect($url)
    {
        header("Location: " . $url);
        exit();
    }

    /**
     * @param bool $action
     * @param bool $controller
     * @param bool $module
     * @param array $params
     * @return $this
     */
    public function duplicateWithParams($action = false, $controller = false, $module = false, $params = [])
    {
        /** @var self $newRequest */
        $newRequest = $this->duplicate();
        $newRequest->setActionName($action);
        $newRequest->setControllerName($controller);
        $newRequest->setModuleName($module);
        $newRequest->attributes->add($params);

        return $newRequest;
    }

    /**
     * Set the controller name to use
     *
     * @param string $value
     * @return self
     */
    public function setControllerName($value)
    {
        if ($value) {
            $this->controller = $value;
        }

        return $this;
    }

    /**
     * Set the module name to use
     * @param string $value
     * @return self
     */
    public function setModuleName($value)
    {
        if ($value) {
            $this->module = $value;
        }

        return $this;
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return !empty($this->get($offset));
    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->attributes->set($offset, $value);
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        $this->attributes->remove($offset);
    }

    /**
     * @return bool
     */
    public function isMaliciousUri()
    {
        $uri = $this->path();
        if (in_array($uri, self::getMaliciousUriArray())) {
            return true;
        }
        return false;
    }

    /**
     * Get the current path info for the request.
     *
     * @return string
     */
    public function path()
    {
        $pattern = trim($this->getPathInfo(), '/');
        return $pattern == '' ? '/' : '/' . $pattern;
    }

    /**
     * @return array
     */
    public static function getMaliciousUriArray()
    {
        return [
            '/wp-login.php',
            '/wp-admin/',
            '/xmlrpc.php',
            '/old/wp-admin/',
            '/wp/wp-admin/',
            '/wordpress/wp-admin/',
            '/blog/wp-admin/',
            '/test/wp-admin/',
        ];
    }

    /**
     * @return array|mixed|string
     */
    protected function prepareRequestUri()
    {
        if ((int)$this->server->get('REDIRECT_STATUS', '200') >= 400 && $this->server->has('REDIRECT_URL')) {
            $requestUri = $this->server->get('REDIRECT_URL');
            $schemeAndHttpHost = $this->getSchemeAndHttpHost();
            if (strpos($requestUri, $schemeAndHttpHost) === 0) {
                $requestUri = substr($requestUri, strlen($schemeAndHttpHost));
            }
            $this->server->set('REQUEST_URI', $requestUri);
            return $requestUri;
        }

        return parent::prepareRequestUri();
    }
}
