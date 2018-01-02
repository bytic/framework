<?php

namespace Nip;

use Nip\Config\ConfigAwareTrait;
use Nip\Dispatcher\Dispatcher;
use Nip\Dispatcher\DispatcherAwareTrait;
use Nip\Http\Response\Response;
use Nip\Http\Response\ResponseAwareTrait;
use Nip\Utility\Traits\NameWorksTrait;
use Nip_Flash_Messages as FlashMessages;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
    use ConfigAwareTrait;
    use DispatcherAwareTrait;
    use ResponseAwareTrait;

    protected $fullName = null;

    protected $name = null;

    protected $action = null;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Helpers\AbstractHelper[]
     */
    protected $helpers = [];

    /**
     * Controller constructor.
     */
    public function __construct()
    {
        $name       = str_replace("Controller", "", get_class($this));
        $this->name = inflector()->unclassify($name);
    }

    /**
     * @param $name
     * @param $arguments
     *
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
     *
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
     *
     * @return Response
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
        if ( ! $this->request instanceof Request) {
            $this->request = new Request();
        }

        return $this->request;
    }

    /**
     * @param Request $request
     *
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
        $this->name   = $request->getControllerName();
        $this->action = $request->getActionName();
    }

    /**
     * @param bool $action
     *
     * @return Response
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

                return $this->getResponse();
            } else {
                throw new NotFoundHttpException('Controller method [' . $action . '] not found for ' . get_class($this));
            }
        }

        throw new NotFoundHttpException('No action specified for ' . get_class($this));
    }

    /**
     * @param $action
     *
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
     * @param bool $action
     * @param bool $controller
     * @param bool $module
     * @param array $params
     *
     * @return mixed
     */
    public function call($action = false, $controller = false, $module = false, $params = [])
    {
        $newRequest = $this->getRequest()->duplicateWithParams($action, $controller, $module, $params);

        $controller = $this->getDispatcher()->generateController($newRequest);
        $controller = $this->prepareCallController($controller, $newRequest);

        return call_user_func_array([$controller, $action], $params);
    }

    /**
     * @param self $controller
     * @param Request $newRequest
     *
     * @return Controller
     */
    protected function prepareCallController($controller, $newRequest)
    {
        $controller->setRequest($newRequest);
        $controller->populateFromRequest($newRequest);

        return $controller;
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
     *
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
        return app('kernel');
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
        $this->setName($this->getFullName());
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
        header("Location: " . $url);
        exit();
    }
}
