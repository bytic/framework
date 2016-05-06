<?php

/**
 * Nip Framework
 *
 * @category   Nip
 * @copyright  2009 Nip Framework
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 * @version    SVN: $Id: Http.php 135 2009-05-27 16:48:23Z victor.stanciu $
 */
class Nip_Request_ProjectDirectory
{
    protected $_requestURI;
    protected $_requestURIParsed;

    protected $_scriptName;
    protected $_scriptNameParsed;

    public function __construct()
    {
    }

    public function determine()
    {
        if ($this->getRequestURI()) {
            $this->prepareURI();
            $this->prepareScriptName();
            return $this->calculate();
        }
        
        return "/";
    }

    public function calculate()
    {
        $projectDir = array();
        foreach ($this->_requestURIParsed as $key => $value) {
            if (isset($this->_scriptNameParsed[$key]) && $this->_scriptNameParsed[$key] == $value) {
                $projectDir[] = $this->_scriptNameParsed[$key];
            }
        }
        return rtrim(implode('/', $projectDir), "/")."/";
    }

    public function setRequestURI($uri)
    {
        $this->_requestURI = $uri;
    }

    public function getRequestURI()
    {
        if (!$this->_requestURI && isset($_SERVER['REQUEST_URI'])) {
            $this->_requestURI = $_SERVER['REQUEST_URI'];
        }
        return $this->_requestURI;
    }

    public function prepareURI()
    {        
        $uri = parse_url($this->getRequestURI());
        $uri = explode("/", $uri['path']);

        if (end($uri) == 'index.php') {
            array_pop($uri);
        }

        $this->_requestURIParsed = $uri;
    }

    public function setScriptName($name)
    {
        $this->_scriptName = $name;
    }

    public function getScriptName()
    {
        if (!$this->_scriptName) {
            if (!class_exists('Nip_Request')) {
                require_once NIP_PATH.'Request.php';
            }
            $this->_scriptName = Nip_Request::instance()->getHttp()->getScriptName();
        }
        return $this->_scriptName;
    }

    public function prepareScriptName()
    {
        $this->_scriptNameParsed = explode("/", $this->getScriptName());
    }

}