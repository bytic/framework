<?php

class Nip_Request
{
    protected $_data = array();
    protected $_http;

    public function __construct()
    {
        $this->setParams($_GET);
        $this->setParams($_POST);
    }

    public function __get($name)
    {
        return $this->_data[$name];
    }

    public function __set($name, $value)
    {
        $this->_data[$name] = $value;
    }

    public function __isset($name)
    {
        return isset($this->_data[$name]);
    }

    public function __unset($name)
    {
        unset($this->_data[$name]);
    }

    public function toArray()
    {
        $vars = get_object_vars($this);
        return $vars['_data'];
    }

    /**
     * Returns Http object
     * @return Nip_Request_Http
     */
    public function getHttp()
    {
        if (!$this->_http) {
            if (!class_exists('Nip_Request_Http')) {
                require NIP_PATH.'request/Http.php';
            }

            $this->_http = new Nip_Request_Http();
        }
        return $this->_http;
    }

    public function setParams(array $params)
    {
        foreach ($params as $param => $value) {
            $this->$param = $value;
        }

        return $this;
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
        $request    = parse_url($_SERVER['REQUEST_URI']);

        if ($components['path'] != $request['path']) {
            $redirect = $url.($request['query'] ? '?'.$request['query'] : '');

            header("Location: ".$redirect, true, $code);
            exit();
        }
    }

    public function redirect($url)
    {
        header("Location: ".$url);
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