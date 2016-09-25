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
     * @var FrontController
     */
    protected $frontController;

    protected $fullName = null;

    protected $name = null;

    protected $action = null;

    /**
     * @var Request
     */
    protected $request;

    protected $config;

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
        $this->name = inflector()->unclassify($name);
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

        return trigger_error("Call to undefined method [$name] in controller [{$this->getClassName()}]", E_USER_ERROR);
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
     * @return string
     */
    public function getClassName()
    {
        return str_replace("Controller", "", get_class($this));
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
        $this->name = $request->getControllerName();
        $this->action = $request->getActionName();
    }

    /**
     * @param bool $action
     * @return bool
     */
    public function dispatchAction($action = false)
    {
        $action = Dispatcher::formatActionName($action);

        if ($action) {
            if ($this->validAction($action)) {
                $this->setAction($action);

                $this->parseRequest();
                $this->beforeAction();
                $this->{$action}();
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

    /**
     * @param $action
     * @return bool
     */
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
        $this->frontController = $dispatcher->getFrontController();

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
        if (!$this->config instanceof \Nip_Config) {
            $this->config = \Nip_Config::instance();
        }

        return $this->config;
    }

    /**
     * Returns the dispatcher Object
     * @return FrontController
     */
    public function getFrontController()
    {
        return $this->frontController;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param string $action
     * @return self
     */
    public function setAction($action)
    {
        $this->action = $action;

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
        if ($this->name === null) {
            $this->name = $this->getFullName();
        }

        return $this->name;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        if ($this->fullName === null) {
            $this->fullName = inflector()->unclassify($this->getClassName());
        }

        return $this->fullName;
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
