<?php

namespace Nip;

use Nip_Flash_Messages as FlashMessages;

/**
 * Class Controller
 * @package Nip
 *
 * @method \Nip_Helper_Url Url()
 * @method \Nip_Helper_Arrays Arrays()
 * @method \Nip_Helper_Async Async()
 */
class Controller
{

    /**
     * @var null|Dispatcher
     */
    protected $dispatcher = null;

    protected $_frontController;

    protected $_fullName = null;
    protected $_name = null;
    protected $_action = null;

    protected $_request;
    protected $_config;
    protected $_helpers = [];

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

        return trigger_error("Call to undefined method $name", E_USER_ERROR);
    }

    public function getHelper($name)
    {
        return HelperBroker::get($name);
    }

    /**
     * @param null|Request $request
     * @return bool
     */
    public function dispatch($request = null)
    {
        $request = $request ? $request : $this->getRequest();
        $this->populateFromRequest($request);

        return $this->dispatchAction($request->getActionName());
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
     * @param Request $request
     */
    public function populateFromRequest(Request $request)
    {
        $this->_name = $request->getControllerName();
        $this->_action = $request->getActionName();
    }

    public function dispatchAction($action = false)
    {
        $action = Dispatcher::formatActionName($action);

        if ($action) {
            if ($this->validAction($action)) {
                $this->setAction($action);

                $this->parseRequest();
                $this->beforeAction();
                $this->{$this->_action}();
                $this->afterAction();

                return true;
            } else {
                $this->getDispatcher()->throwError('Action ['.$action.'] is not valid for '.get_class($this));
            }
        } else {
            trigger_error('No action specified', E_USER_ERROR);
        }

        return false;
    }

    protected function validAction($action)
    {
        return in_array($action, get_class_methods(get_class($this)));
    }

    /**
     * Called before action
     */
    protected function parseRequest()
    {
        return true;
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

    /**
     * Returns the dispatcher Object
     * @return Dispatcher
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * @param Dispatcher $dispatcher
     * @return self
     */
    public function setDispatcher(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
        $this->_frontController = $dispatcher->getFrontController();

        return $this;
    }

    public function call($action = false, $controller = false, $module = false, $params = array())
    {
        $newRequest = $this->getRequest()->duplicateWithParams($action, $controller, $module, $params);

        $controller = $this->getDispatcher()->generateController($newRequest);
        $controller->setView($this->getView());
        $controller->setRequest($newRequest);
        $controller->populateFromRequest($newRequest);

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
     * Returns the dispatcher Object
     * @return FrontController
     */
    public function getFrontController()
    {
        return $this->_frontController;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->_action;
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

    protected function forward($action = false, $controller = false, $module = false, $params = array())
    {
        $this->getDispatcher()->forward($action, $controller, $module, $params);
    }

    protected function flashRedirect($message, $url, $type = 'success', $name = false)
    {
        $name = $name ? $name : $this->getName();
        FlashMessages::instance()->add($name, $type, $message);
        $this->redirect($url);
    }

    public function getName()
    {
        if ($this->_name === null) {
            $this->_name = $this->getFullName();
        }

        return $this->_name;
    }

    public function getFullName()
    {
        if ($this->_fullName === null) {
            $this->_fullName = inflector()->unclassify($this->getClassName());

        }

        return $this->_fullName;
    }

    public function getClassName()
    {
        return str_replace("Controller", "", get_class($this));
    }

    protected function redirect($url, $code = null)
    {
        switch ($code) {
            case '301' :
                header("HTTP/1.1 301 Moved Permanently");
                break;
        }
        header("Location: ".$url);
        exit();
    }
}