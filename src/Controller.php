<?php

namespace Nip;

/**
 * Class Controller
 * @package Nip
 *
 * @method \Nip_Helper_View_URL URL()
 */
class Controller
{

    protected $_dispatcher;
    protected $_frontController;

    protected $_action;
    protected $_name;
    protected $_request;
    protected $_config;
    protected $_helpers = array();

    public function __construct()
    {
        $name = str_replace("Controller", "", get_class($this));
        $this->_name = inflector()->unclassify($name);
    }


    public function __call($name, $arguments)
    {
        if ($name === ucfirst($name)) {
            return $this->getHelper($name);
        }

        trigger_error("Call to undefined method $name", E_USER_ERROR);
        return;
    }

    public function getHelper($name)
    {
        return HelperBroker::get($name);
    }

    public function dispatch($request = null)
    {
        $request = $request ? $request : $this->getRequest();
        $this->_name = $request->getControllerName();
        $this->_action = $request->getActionName();
        return $this->dispatchAction($request->getActionName());
    }

    public function dispatchAction($action = false)
    {
        $action = Dispatcher::formatActionName($action);

        if ($action) {
            if ($this->validAction($action)) {
                $this->setAction($action);

                $this->beforeAction();
                $this->{$this->_action}();
                $this->afterAction();
                return true;
            } else {
                $this->getDispatcher()->throwError('Action [' . $action . '] is not valid for ' . get_class($this));
            }
        } else {
            trigger_error('No action specified', E_USER_ERROR);
        }
        return false;
    }

    public function call($action = false, $controller = false, $module = false, $params = array())
    {
        $newRequest = $this->getRequest()->duplicateWithParams($action, $controller, $module, $params);

        $controller = $this->getDispatcher()->generateController($newRequest);
        $controller->setView($this->getView());
        $controller->setRequest($newRequest);
        return call_user_func_array(array($controller, $action), $params);
    }

    /**
     * Returns the config Object
     * @return \Nip_Config
     */
    public function getConfig()
    {
        if (!$this->_config instanceof \Nip_Config) {
            $this->_config = \Nip_Config::instance();
        }
        return $this->_config;
    }

    /**
     * Returns the request Object
     * @return Request
     */
    public function getRequest()
    {
        if (!$this->_request instanceof Request) {
            $this->_request = new Request();
        }
        return $this->_request;
    }

    /**
     * @param Request $request
     * @return self
     */
    public function setRequest(Request $request)
    {
        $this->_request = $request;
        return $this;
    }

    /**
     * Returns the dispatcher Object
     * @return Dispatcher
     */
    public function getDispatcher()
    {
        return $this->_dispatcher;
    }

    /**
     * @param Dispatcher $dispatcher
     * @return self
     */
    public function setDispatcher(Dispatcher $dispatcher)
    {
        $this->_dispatcher = $dispatcher;
        $this->_frontController = $dispatcher->getFrontController();
        return $this;
    }

    /**
     * Returns the dispatcher Object
     * @return FrontController
     */
    public function getFrontController()
    {
        return $this->_frontController;
    }

    /**
     * @param string $action
     * @return self
     */
    public function setAction($action)
    {
        $this->_action = $action;
        return $this;
    }

    /**
     * Called before $this->action
     */
    protected function beforeAction()
    {
        return true;
    }

    /**
     * Called after $this->action
     */
    protected function afterAction()
    {
        return true;
    }

    protected function validAction($action)
    {
        return in_array($action, get_class_methods(get_class($this)));
    }

    public function getName()
    {
        return $this->_name;
    }
}