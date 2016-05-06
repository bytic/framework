<?php

class Nip_FrontController
{
    protected $_router;
    protected $_dispatcher;
    protected $_request;
    protected $_requestURI = null;

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
            $this->_requestURI = str_replace("###".Nip_Staging::instance()->getStage()->getProjectDir(),
                "", "###".$url['path']);
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
            $this->_dispatcher = Nip_Dispatcher::instance();
        }

        return $this->_dispatcher;
    }

    public function setDispatcher($dispatcher = false)
    {
        $this->_dispatcher = $dispatcher;
        return $this;
    }

    public function getRequest()
    {
        if (!$this->_request) {
            $this->_request = Nip_Request::instance();
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