<?php

namespace Nip;

class FrontController
{

    protected $_bootstrap;

    protected $_staging;
    protected $_stage;
    
    protected $_router;
    protected $_dispatcher;
    protected $_request;
    protected $_requestURI = null;

    public function setBootstrap($bootstrap)
    {
        $this->_bootstrap = $bootstrap;
    }

    /**
     * @return mixed
     */
    public function getStage()
    {
        return $this->_stage;
    }

    /**
     * @param mixed $stage
     */
    public function setStage($stage)
    {
        $this->_stage = $stage;
    }

    /**
     * @return mixed
     */
    public function getStaging()
    {
        return $this->_staging;
    }

    /**
     * @param mixed $staging
     */
    public function setStaging($staging)
    {
        $this->_staging = $staging;
    }

    public function dispatch($params = array())
    {
        $this->getDispatcher()->dispatch($params['action'], $params['controller'], $params['module']);
    }

    public function routeURI($uri = false)
    {
        $uri = $uri ? $uri : $this->getRequestURI();
        $params = $this->getRouter()->route($uri);
        $this->getRequest()->setParams($params);
        return $params;
    }

    public function getRequestURI()
    {
        if ($this->_requestURI === null) {
            $url = parse_url($_SERVER['REQUEST_URI']);

            // replace first occurence
            $this->_requestURI = str_replace("###" . $this->getStage()->getProjectDir(),
                "", "###" . $url['path']);
        }
        return $this->_requestURI;
    }

    public function getRouter()
    {
        if (!$this->_router) {
            $this->_router = new Nip_Router();
        }

        return $this->_router;
    }

    public function setRouter(Nip_Router $router = NULL)
    {
        $this->_router = $router;
        return $this;
    }

    /**
     * @return Nip_Dispatcher
     */
    public function getDispatcher()
    {
        if (!$this->_dispatcher) {
            $this->_dispatcher = $this->initDispatcher();
        }

        return $this->_dispatcher;
    }

    public function initDispatcher()
    {
        $dispatcher = Nip_Dispatcher::instance();
        $dispatcher->setFrontController($this);
        return $dispatcher;
    }

    public function setDispatcher($dispatcher = false)
    {
        $this->_dispatcher = $dispatcher;
        return $this;
    }

    public function getRequest()
    {
        if (!$this->_request) {
            $this->_request = new Request();
        }

        return $this->_request;
    }

    public function setRequest($request = false)
    {
        $this->_request = $request;
        return $this;
    }

    public function getTrace()
    {
        return Nip_FrontController_Trace::instance();
    }

    /**
     * Singleton
     *
     * @return Nip_FrontController
     */
    static public function instance()
    {
        static $instance;
        if (!($instance instanceof self)) {
            $instance = new self;
        }
        return $instance;
    }
}