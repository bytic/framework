<?php

namespace Nip\Router\Route;

abstract class AbstractRoute
{
    /**
     * @var string
     */
    protected $_name = null;

    /**
     * @var string
     */
    protected $_type;

    protected $_parser = null;

    protected $_base;

    protected $_request = null;

    /**
     * @var string
     */
    protected $_uri;

    public function __construct($map = false, $params = array())
    {
        if ($map) {
            $this->getParser()->setMap($map);
        }

        if ($params) {
            $this->getParser()->setParams($params);
        }
        $this->init();
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array(array($this->getParser(), $name), $arguments);
    }

    public function assemble($params = array())
    {
        return $this->getParser()->assemble($params);
    }

    public function init()
    {
    }

    public function setBase($base)
    {
        $this->_base = $base;
    }

    public function getBase($params = array())
    {
        $this->initBase();
        if ($params['_subdomain']) {
            $base = $this->replaceSubdomain($params['_subdomain'], $this->_base);
            return $base;
        }
        return $this->_base;
    }

    public function replaceSubdomain($subdomain, $url)
    {
        $host = parse_url($url, PHP_URL_HOST);
        $parts = explode('.', $host);
        if (count($parts) > 2)
            array_shift($parts);

        array_unshift($parts, $subdomain);
        $newHost = implode('.', $parts);

        return str_replace($host, $newHost, $url);
    }

    public function initBase($params = array())
    {
        if (!$this->_base) {
            $this->_base = BASE_URL;
        }
    }

    public function assembleFull($params = array())
    {
        $base = $this->getBase($params);
        $base = rtrim($base, "/");
        return $base . $this->assemble($params);
    }


    public function match($uri)
    {
        $this->_uri = $uri;
        if ($this->domainCheck()) {
            $return = $this->getParser()->match($uri);
            if ($return === true) {
                $this->postMatch();
            }
            return $return;
        }
        return false;
    }

    public function postMatch()
    {
    }

    public function domainCheck()
    {
        return true;
    }

    public function setType($type)
    {
        $this->_type = $type;
        return $this;
    }

    public function getType()
    {
        if (!$this->_type) {
            $name = get_class($this);
            $parts = explode('_', $name);
            $this->_type = strtolower(end($parts));
        }
        return $this->_type;
    }

    /**
     * @return \Nip\Router\Parser\AbstractParser
     */
    public function getParser()
    {
        if ($this->_parser === null) {
            $this->initParser();
        }
        return $this->_parser;
    }

    public function initParser()
    {
        $class = $this->getParserClass();
        $parser = new $class;
        $this->setParser($parser);
    }

    public function getParserClass()
    {
        return 'Nip\Router\Parser\\' . inflector()->camelize($this->getType());
    }

    public function setParser($class)
    {
        $this->_parser = $class;
        return $this;
    }


    /**
     * @return \Nip\Request
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

    public function populateRequest()
    {
        $params = $this->getParams();
        foreach ($params as $param => $value) {
            switch ($param) {
                case 'module':
                    $this->getRequest()->setModuleName($value);
                    break;
                case 'controller':
                    $this->getRequest()->setControllerName($value);
                    break;
                case 'action':
                    $this->getRequest()->setActionName($value);
                    break;
                default:
                    $this->getRequest()->attributes->set($param, $value);
                    break;
            }
        }
        $this->getRequest()->attributes->add($this->getMatches());
    }

    public function getUri()
    {
        return $this->_uri;
    }

    /**
     * @return string
     */
    public function getName()
    {
        if($this->_name == null) {
            $this->initName();
        }
        return $this->_name;
    }

    /**
     * @return string
     */
    public function initName()
    {
        $this->setName($this->getClassName());
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->_name = $name;
    }

    public function getClassName()
    {
        return get_class($this);
    }
}