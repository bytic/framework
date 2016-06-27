<?php

/**
 * Inspired From Symfony Symfony\Component\HttpFoundation\Request class
 */


namespace Nip;

use Nip\Request\ParameterBag;
use Nip\Request\FileBag;
use Nip\Request\ServerBag;
use Nip\Request\HeaderBag;

class Request
{
    /**
     * Has the action been dispatched?
     * @var boolean
     */
    protected $_dispatched = false;

    /**
     * Module
     * @var string
     */
    protected $_module;

    /**
     * Module key for retrieving module from params
     * @var string
     */
    protected $_moduleKey = 'module';

    /**
     * Controller
     * @var string
     */
    protected $_controller;

    /**
     * Controller key for retrieving controller from params
     * @var string
     */
    protected $_controllerKey = 'controller';

    /**
     * Action
     * @var string
     */
    protected $_action;

    /**
     * Action key for retrieving action from params
     * @var string
     */
    protected $_actionKey = 'action';

    /**
     * Custom parameters.
     * @var \Nip\Request\ParameterBag
     */
    public $attributes;

    /**
     * Request body parameters ($_POST).
     * @var \Nip\Request\ParameterBag
     */
    public $body;

    /**
     * Query string parameters ($_GET).
     * @var \Nip\Request\ParameterBag
     */
    public $query;

    /**
     * Server and execution environment parameters ($_SERVER).
     * @var \Nip\Request\ParameterBag
     */
    public $server;

    /**
     * Uploaded files ($_FILES).
     * @var \Nip\Request\FileBag
     */
    public $files;

    /**
     * Cookies ($_COOKIE).
     * @var \Nip\Request\ParameterBag
     */
    public $cookies;

    protected $_http;

    public function __construct()
    {
        $this->body = new ParameterBag();
        $this->query = new ParameterBag();
        $this->attributes = new ParameterBag();
        $this->cookies = new ParameterBag();
        $this->files = new FileBag();
        $this->server = new ServerBag();
        $this->headers = new HeaderBag();
    }

    /**
     * Sets the parameters for this request.
     *
     * This method also re-initializes all properties.
     *
     * @param array $query The GET parameters
     * @param array $body The POST parameters
     * @param array $attributes The request attributes (parameters parsed from the PATH_INFO, ...)
     * @param array $cookies The COOKIE parameters
     * @param array $files The FILES parameters
     * @param array $server The SERVER parameters
     * @param string|resource $content The raw body data
     */
    public function initialize(array $query = array(), array $body = array(), array $attributes = array(), array $cookies = array(), array $files = array(), array $server = array(), $content = null)
    {
        $this->body->replace($body);
        $this->query->replace($query);
        $this->attributes->replace($attributes);
        $this->cookies->replace($cookies);
        $this->files->replace($files);
        $this->server->replace($server);
        $this->headers->replace($this->server->getHeaders());
        $this->content = $content;
    }

    /**
     * Creates a new request with values from PHP's super globals.
     * @return Request A new request
     */
    public static function createFromGlobals()
    {
        $server = $_SERVER;
        if ('cli-server' === PHP_SAPI) {
            if (array_key_exists('HTTP_CONTENT_LENGTH', $_SERVER)) {
                $server['CONTENT_LENGTH'] = $_SERVER['HTTP_CONTENT_LENGTH'];
            }
            if (array_key_exists('HTTP_CONTENT_TYPE', $_SERVER)) {
                $server['CONTENT_TYPE'] = $_SERVER['HTTP_CONTENT_TYPE'];
            }
        }

        $request = new self();
        $request->initialize($_GET, $_POST, array(), $_COOKIE, $_FILES, $server);

        if (0 === strpos($request->headers->get('CONTENT_TYPE'), 'application/x-www-form-urlencoded')
            && in_array(strtoupper($request->server->get('REQUEST_METHOD', 'GET')), array('PUT', 'DELETE', 'PATCH'))
        ) {
            parse_str($request->getContent(), $data);
            $request->body = new ParameterBag($data);
        }

        return $request;
    }

    /**
     * Creates a Request based on a given URI and configuration.
     *
     * The information contained in the URI always take precedence
     * over the other information (server and parameters).
     *
     * @param string $uri The URI
     * @param string $method The HTTP method
     * @param array $parameters The query (GET) or request (POST) parameters
     * @param array $cookies The request cookies ($_COOKIE)
     * @param array $files The request files ($_FILES)
     * @param array $server The server parameters ($_SERVER)
     * @param string $content The raw body data
     *
     * @return Request A Request instance
     */
    public static function create($uri, $method = 'GET', $parameters = array(), $cookies = array(), $files = array(), $server = array(), $content = null)
    {
        $components = parse_url($uri);
        if (isset($components['host'])) {
            $server['SERVER_NAME'] = $components['host'];
            $server['HTTP_HOST'] = $components['host'];
        }
        if (isset($components['scheme'])) {
            if ('https' === $components['scheme']) {
                $server['HTTPS'] = 'on';
                $server['SERVER_PORT'] = 443;
            } else {
                unset($server['HTTPS']);
                $server['SERVER_PORT'] = 80;
            }
        }
        if (isset($components['port'])) {
            $server['SERVER_PORT'] = $components['port'];
            $server['HTTP_HOST'] = $server['HTTP_HOST'] . ':' . $components['port'];
        }
        if (isset($components['user'])) {
            $server['PHP_AUTH_USER'] = $components['user'];
        }
        if (isset($components['pass'])) {
            $server['PHP_AUTH_PW'] = $components['pass'];
        }
        if (!isset($components['path'])) {
            $components['path'] = '/';
        }
        switch (strtoupper($method)) {
            case 'POST':
            case 'PUT':
            case 'DELETE':
                if (!isset($server['CONTENT_TYPE'])) {
                    $server['CONTENT_TYPE'] = 'application/x-www-form-urlencoded';
                }
            // no break
            case 'PATCH':
                $body = $parameters;
                $query = array();
                break;
            default:
                $body = array();
                $query = $parameters;
                break;
        }
        $queryString = '';
        if (isset($components['query'])) {
            parse_str(html_entity_decode($components['query']), $qs);
            if ($query) {
                $query = array_replace($qs, $query);
                $queryString = http_build_query($query, '', '&');
            } else {
                $query = $qs;
                $queryString = $components['query'];
            }
        } elseif ($query) {
            $queryString = http_build_query($query, '', '&');
        }
        $server['REQUEST_URI'] = $components['path'] . ('' !== $queryString ? '?' . $queryString : '');
        $server['QUERY_STRING'] = $queryString;

        $request = new self();
        $request->initialize($query, $body, array(), $cookies, $files, $server, $content);

        return $request;

    }

    /**
     * Gets a "parameter" value from any bag.
     *
     * This method is mainly useful for libraries that want to provide some flexibility. If you don't need the
     * flexibility in controllers, it is better to explicitly get request parameters from the appropriate
     * public property instead (attributes, query, request).
     *
     * Order of precedence: PATH (routing placeholders or custom attributes), GET, BODY
     *
     * @param string $key the key
     * @param mixed $default the default value if the parameter key does not exist
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if ($this !== $result = $this->attributes->get($key, $this)) {
            return $result;
        }
        if ($this !== $result = $this->query->get($key, $this)) {
            return $result;
        }
        if ($this !== $result = $this->body->get($key, $this)) {
            return $result;
        }
        return $default;
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
     * Retrieve the module name
     * @return string
     */
    public function getModuleName()
    {
        if (null === $this->_module) {
            $this->_module = $this->attributes->get($this->getModuleKey());
        }

        return $this->_module;
    }

    /**
     * Set the module name to use
     * @param string $value
     * @return self
     */
    public function setModuleName($value)
    {
        $this->_module = $value;
        return $this;
    }

    /**
     * Retrieve the controller name
     * @return string
     */
    public function getControllerName()
    {
        if (null === $this->_controller) {
            $this->_controller = $this->attributes->get($this->getControllerKey());
        }

        return $this->_controller;
    }

    /**
     * Set the controller name to use
     *
     * @param string $value
     * @return self
     */
    public function setControllerName($value)
    {
        $this->_controller = $value;
        return $this;
    }

    /**
     * Retrieve the action name
     *
     * @return string
     */
    public function getActionName()
    {
        if (null === $this->_action) {
            $this->_action = $this->attributes->get($this->getActionKey());
            if ($this->_action==null){
                $this->setActionName($this->getActionDefault());
            }
        }

        return $this->_action;
    }

    public function getActionDefault()
    {
        return 'index';
    }

    /**
     * Set the action name
     * @param string $value
     * @return self
     */
    public function setActionName($value)
    {
        $this->_action = $value;
        if (null === $value) {
            $this->setParam($this->getActionKey(), $value);
        }
        return $this;
    }

    /**
     * Retrieve the module key
     *
     * @return string
     */
    public function getModuleKey()
    {
        return $this->_moduleKey;
    }

    /**
     * Set the module key
     *
     * @param string $key
     * @return self
     */
    public function setModuleKey($key)
    {
        $this->_moduleKey = (string)$key;
        return $this;
    }

    /**
     * Retrieve the controller key
     *
     * @return string
     */
    public function getControllerKey()
    {
        return $this->_controllerKey;
    }

    /**
     * Set the controller key
     *
     * @param string $key
     * @return self
     */
    public function setControllerKey($key)
    {
        $this->_controllerKey = (string)$key;
        return $this;
    }

    /**
     * Retrieve the action key
     *
     * @return string
     */
    public function getActionKey()
    {
        return $this->_actionKey;
    }

    /**
     * Set the action key
     *
     * @param string $key
     * @return self
     */
    public function setActionKey($key)
    {
        $this->_actionKey = (string)$key;
        return $this;
    }


    public function getMCA()
    {
        return $this->getModuleName().'.'.$this->getControllerName().'.'.$this->getActionName();
    }



    public function setParams(array $params)
    {
        foreach ($params as $param => $value) {
            $this->$param = $value;
        }

        return $this;
    }

    /**
     * Returns Http object
     * @return Nip_Request_Http
     */
    public function getHttp()
    {
        if (!$this->_http) {
            $this->_http = new Request\Http();
            $this->_http->setRequest($this);
        }
        return $this->_http;
    }

    public function isCLI()
    {
        return php_sapi_name() == 'cli';
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

    public function redirect($url)
    {
        header("Location: " . $url);
        exit();
    }

    /**
     * Singleton
     *
     * @return Nip_Request
     */
    static public function instance()
    {
        static $instance;
        if (!($instance instanceof self)) {
            $instance = new self();
        }
        return $instance;
    }
}