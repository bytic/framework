<?php

namespace Nip;

class Dispatcher
{
    protected $_frontController = false;
    protected $_request;

    protected $_module = "default";
    protected $_controller = "error";
    protected $_action = "index";

    protected $_currentController = false;

    protected $_hops = 0;
    protected $_maxHops = 30;

    public function dispatch(Request $request = null)
    {
        $request = $request ? $request : $this->getRequest();
        $this->_hops++;

        if ($this->_hops <= $this->_maxHops) {
            $this->_module = $request->getModuleName();
            $this->_controller = $request->getControllerName();
            $this->_action = $request->getActionName();

            list($controller, $action) = $this->prepareControllerAction($this->_action, $this->_controller, $this->_module);

            $profilerName = "dispatch [{$this->_module}.{$this->_controller}.{$this->_action}]";
            \Nip_Profiler::instance()->start($profilerName);
            if ($controller instanceof Controller) {
                try {
                    $this->_currentController = $controller;
                    $controller->setRequest($request);
                    $controller->dispatch($action);
                } catch (\Nip_Dispatcher_ForwardException $e) {
                    $return = $this->dispatch();
                    \Nip_Profiler::instance()->end($profilerName);
                    return $return;
                }
            } else {
                $this->setErrorControler();
                $return = $this->dispatch();
                \Nip_Profiler::instance()->end($profilerName);
                return $return;
            }
        } else {
            trigger_error("Maximum number of hops ($this->_maxHops) has been reached for {$this->_module}-{$this->_controller}-{$this->_action}", E_USER_ERROR);
        }

        \Nip_Profiler::instance()->end($profilerName);
        return true;
    }

    public function forward($action = false, $controller = false, $module = false, $params = array())
    {
        $this->_action = $action;

        if ($controller) {
            $this->_controller = $controller;
        }
        if ($module) {
            $this->_module = $module;
        }

        if (is_array($params)) {
            $this->getRequest()->attributes->add($params);
        }

        throw new \Nip_Dispatcher_ForwardException;
    }

    public function prepareControllerAction($action = false, $controllerClass = false, $module = false)
    {
        $module = $module ? $module : $this->_module;
        $controllerClass = $controllerClass ? $controllerClass : $this->_controller;
        $action = $action ? $action : $this->_action;

        $controllerClass = $this->getFullControllerName($module, $controllerClass);
        $action = $this->formatActionName($action);

        try {
            AutoLoader::instance()->load($controllerClass);
        } catch (AutoLoader\Exception $e) {
            $this->getFrontController()->getTrace()->add($e->getMessage());
            return;
        }

        /* @var $controllerClass \Nip_Controller */
        $controller = $this->newController($controllerClass);
        return array($controller, $action);
    }

    public function newController($class)
    {
        $controller = new $class();
        $controller->setDispatcher($this);
        return $controller;
    }

    public function setErrorControler()
    {
        $this->_action = 'index';
        if ($this->_controller == 'error') {
            $this->_module = 'default';
        } else {
            $this->_controller = 'error';
//            $this->_module = 'default';
        }
    }

    public function throwError($params = false)
    {
        $this->getFrontController()->getTrace()->add($params);
        $this->setErrorControler();
        $this->forward('index');
        return;
    }

    public static function reverseControllerName($controller)
    {
        return inflector()->unclassify($controller);
    }

    public static function getControllerName($controller)
    {
        return inflector()->classify($controller);
    }

    public function getFullControllerName($module, $controller)
    {
        return inflector()->camelize($module) . "_" . $this->getControllerName($controller) . "Controller";
    }

    protected function formatActionName($action)
    {
        $action = inflector()->camelize($action);
        $action[0] = strtolower($action[0]);

        return $action;
    }

    public function getFrontController()
    {
        if (!$this->_frontController) {
            $this->_frontController = \Nip_FrontController::instance();
        }

        return $this->_frontController;
    }

    public function setFrontController(FrontController $controller)
    {
        $this->_frontController = $controller;
        $this->_request = $controller->getRequest();
        return $this;
    }

    public function getCurrentController()
    {
        return $this->_currentController;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->_request;
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
            $instance = new self();
        }
        return $instance;
    }

}