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

    public function dispatch(Request $request = null)
    {
        $this->getDispatcher()->dispatch($request);
    }

    public function route($request = false)
    {
        $request = $request ? $request : $this->getRequest();
        $params = $this->getRouter()->route($request);
        return $params;
    }

    public function getRouter()
    {
        if (!$this->_router) {
            $this->_router = new \Nip_Router();
        }

        return $this->_router;
    }

    public function setRouter(\Nip_Router $router = NULL)
    {
        $this->_router = $router;
        return $this;
    }

    /**
     * @return Dispatcher
     */
    public function getDispatcher()
    {
        if (!$this->_dispatcher) {
            $this->initDispatcher();
        }

        return $this->_dispatcher;
    }

    public function initDispatcher()
    {
        $this->_dispatcher = $this->newDispatcher();
    }

    /**
     * @return Dispatcher
     */
    public function newDispatcher()
    {
        $dispatcher = Dispatcher::instance();
        $dispatcher->setFrontController($this);
        return $dispatcher;
    }

    /**
     * @param bool|Dispatcher $dispatcher
     * @return $this
     */
    public function setDispatcher($dispatcher = false)
    {
        $this->_dispatcher = $dispatcher;
        return $this;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        if (!$this->_request) {
            $this->_request = new Request();
        }

        return $this->_request;
    }

    /**
     * @param bool|Request $request
     * @return $this
     */
    public function setRequest($request = false)
    {
        $this->_request = $request;
        return $this;
    }

    /**
     * @return FrontController\Trace
     */
    public function getTrace()
    {
        return FrontController\Trace::instance();
    }

    /**
     * Singleton
     *
     * @return self
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