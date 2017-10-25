<?php

namespace Nip\Rest;

class Client
{
    const METHOD_POST = "post";
    const METOD_GET = "get";

    protected $_url;
    protected $_params = [];
    protected $_result;
    protected $_method;

    protected $_timeout = 10;

    public function __construct($url, $method = self::METHOD_POST)
    {
        $this->setURL($url);
        $this->setMethod($method);
    }

    public function setURL($_url)
    {
        $this->_url = $_url;
    }

    public function __call($name, $arguments = array())
    {
        $this->_params[$name] = $arguments[0];
    }

    public function getResult()
    {
        if (!$this->_result) {
            $this->dispatch();
        }

        return $this->_result;
    }

    public function dispatch()
    {
        $ch = curl_init();
        $params = http_build_query($this->_params);

        curl_setopt($ch, CURLOPT_URL, $this->_url . ($this->getMethod() == self::METOD_GET ? '?' . $params : ''));
        if ($this->getMethod() == self::METHOD_POST) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->_timeout);
        $this->_result = curl_exec($ch);

        curl_close($ch);
    }

    public function getMethod()
    {
        if (!$this->_method) {
            $this->_method = "post";
        }
        return $this->_method;
    }

    public function setMethod($method)
    {
        $this->_method = $method;
    }
}
