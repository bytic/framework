<?php

namespace Nip\Router\Route;

use Nip\Router\Parsers\AbstractParser;
use Nip\Utility\Traits\NameWorksTrait;

/**
 * Class AbstractRoute
 * @package Nip\Router\Route
 */
abstract class AbstractRoute
{
    use NameWorksTrait;

    /**
     * @var string
     */
    protected $name = null;

    /**
     * @var string
     */
    protected $type = null;

    protected $parser = null;

    protected $base = null;

    protected $request = null;

    /**
     * @var string
     */
    protected $uri;

    /**
     * AbstractRoute constructor.
     * @param bool $map
     * @param array $params
     */
    public function __construct($map = false, $params = [])
    {
        if ($map) {
            $this->getParser()->setMap($map);
        }

        if (count($params)) {
            $this->getParser()->setParams($params);
        }
        $this->init();
    }

    /**
     * @return AbstractParser
     */
    public function getParser()
    {
        if ($this->parser === null) {
            $this->initParser();
        }

        return $this->parser;
    }

    /**
     * @param $class
     * @return $this
     */
    public function setParser($class)
    {
        $this->parser = $class;

        return $this;
    }

    public function initParser()
    {
        $class = $this->getParserClass();
        $parser = new $class;
        $this->setParser($parser);
    }

    /**
     * @return string
     */
    public function getParserClass()
    {
        return 'Nip\Router\Parsers\\' . inflector()->camelize($this->getType());
    }

    /**
     * @return string
     */
    public function getType()
    {
        if ($this->type === null) {
            $this->initType();
        }

        return $this->type;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    protected function initType()
    {
        $this->setType($this->generateType());
    }

    /**
     * @return string
     */
    protected function generateType()
    {
        if ($this->isNamespaced()) {
            $name = strtolower($this->getClassFirstName());
            return str_replace('route', '', $name);
        }
        $name = get_class($this);
        $parts = explode('_', $name);

        return strtolower(end($parts));
    }

    public function init()
    {
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->getParser(), $name], $arguments);
    }

    /**
     * @param array $params
     * @return string
     */
    public function assembleFull($params = [])
    {
        $base = $this->getBase($params);
        $base = rtrim($base, "/");

        return $base . $this->assemble($params);
    }

    /**
     * @param array $params
     * @return string
     */
    public function getBase($params = [])
    {
        $this->checkBase($params);
        if (isset($params['_subdomain']) && !empty($params['_subdomain'])) {
            $base = $this->replaceSubdomain($params['_subdomain'], $this->base);

            return $base;
        }

        return $this->base;
    }

    /**
     * @param string $base
     */
    public function setBase($base)
    {
        $this->base = $base;
    }

    /** @noinspection PhpUnusedParameterInspection
     * @param array $params
     */
    public function checkBase($params = [])
    {
        if ($this->base === null) {
            $this->initBase($params);
        }
    }

    /** @noinspection PhpUnusedParameterInspection
     * @param array $params
     */
    public function initBase($params = [])
    {
        $this->setBase(\Nip\url()->to('/'));
    }

    /**
     * @param $subdomain
     * @param $url
     * @return mixed
     */
    public function replaceSubdomain($subdomain, $url)
    {
        $host = parse_url($url, PHP_URL_HOST);
        $parts = explode('.', $host);
        if (count($parts) > 2) {
            array_shift($parts);
        }

        array_unshift($parts, $subdomain);
        $newHost = implode('.', $parts);

        return str_replace($host, $newHost, $url);
    }

    /**
     * @param array $params
     * @return string
     */
    public function assemble($params = [])
    {
        return $this->getParser()->assemble($params);
    }

    /**
     * @param $uri
     * @return bool
     */
    public function match($uri)
    {
        $this->uri = $uri;
        if ($this->domainCheck()) {
            $return = $this->getParser()->match($uri);
            if ($return === true) {
                $this->postMatch();
            }

            return $return;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function domainCheck()
    {
        return true;
    }

    public function postMatch()
    {
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

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->getParser()->getParams();
    }

    /**
     * @return \Nip\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param \Nip\Request $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * @return array
     */
    public function getMatches()
    {
        return $this->getParser()->getMatches();
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @return string
     */
    public function getName()
    {
        if ($this->name == null) {
            $this->initName();
        }

        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    public function initName()
    {
        $this->setName($this->getClassName());
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return get_class($this);
    }
}
