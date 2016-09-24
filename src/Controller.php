<?php

namespace Nip;

use Nip\Utility\Traits\NameWorksTrait;
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
    use NameWorksTrait;

    /**
     * @var null|Dispatcher
     */
    protected $dispatcher = null;

    /**
     * @var
     */
    protected $_frontController;

    protected $_fullName = null;
    protected $_name = null;
    protected $_action = null;

    /**
     * @var Request
     */
    protected $request;
    
    protected $_config;

    /**
     * @var Helpers\AbstractHelper[]
     */
    protected $helpers = [];

    /**
     * Controller constructor.
     */
    public function __construct()
    {
        $name = str_replace("Controller", "", get_class($this));
        $this->_name = inflector()->unclassify($name);
    }

    /**
     * @param $name
     * @param $arguments
     * @return bool|mixed
     */
    public function __call($name, $arguments)
    {
        if ($name === ucfirst($name)) {
            return $this->getHelper($name);
        }

        return trigger_error("Call to undefined method $name", E_USER_ERROR);
    }

    /**
     * @param $name
     * @return Helpers\AbstractHelper
     */
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
        if (!$this->request instanceof Request) {
            $this->request = new Request();
        }

        return $this->request;
    }

    /**
     * @param Request $request
     * @return self
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

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

    /**
     * @param bool $action
     * @param bool $controller
     * @param bool $module
     * @param array $params
     * @return mixed
     */
    public function call($action = false, $controller = false, $module = false, $params = [])
    {
        $newRequest = $this->getRequest()->duplicateWithParams($action, $controller, $module, $params);

        $controller = $this->getDispatcher()->generateController($newRequest);
        $controller->setView($this->getView());
        $controller->setRequest($newRequest);
        $controller->populateFromRequest($newRequest);

        return call_user_func_array([$controller, $action], $params);
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

    /**
     * @return string
     */
    public function getRootNamespace()
    {
        return $this->getApplication()->getRootNamespace();
    }

    /**
     * @return Application
     */
    public function getApplication()
    {
        return $this->getDispatcher()->getFrontController()->getApplication();
    }

    /**
     * @param bool $action
     * @param bool $controller
     * @param bool $module
     * @param array $params
     */
    protected function forward($action = false, $controller = false, $module = false, $params = [])
    {
        $this->getDispatcher()->forward($action, $controller, $module, $params);
    }

    /**
     * @param $message
     * @param $url
     * @param string $type
     * @param bool $name
     */
    protected function flashRedirect($message, $url, $type = 'success', $name = false)
    {
        $name = $name ? $name : $this->getName();
        FlashMessages::instance()->add($name, $type, $message);
        $this->redirect($url);
    }

    /**
     * @return string
     */
    public function getName()
    {
        if ($this->_name === null) {
            $this->_name = $this->getFullName();
        }

        return $this->_name;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        if ($this->_fullName === null) {
            $this->_fullName = inflector()->unclassify($this->getClassName());

        }

        return $this->_fullName;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return str_replace("Controller", "", get_class($this));
    }

    /**
     * @param $url
     * @param null $code
     */
    protected function redirect($url, $code = null)
    {
        switch ($code) {
            case '301':
                header("HTTP/1.1 301 Moved Permanently");
                break;
        }
        header("Location: ".$url);
        exit();
    }
}
