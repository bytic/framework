<?php

namespace Nip;

use Nip\Dispatcher\ForwardException;

class Dispatcher
{
    /**
     * @var bool|FrontController
     */
    protected $_frontController = false;


    /**
     * @var null|Request
     */
    protected $request = null;

    protected $_currentController = false;

    protected $_hops = 0;
    protected $_maxHops = 30;

    public static function reverseControllerName($controller)
    {
        return inflector()->unclassify($controller);
    }

    /**
     * @param $name
     * @return mixed
     */
    public static function formatActionName($name)
    {
        $name = inflector()->camelize($name);
        $name[0] = strtolower($name[0]);

        return $name;
    }

    /**
     * Singleton
     *
     * @return self
     */
    public static function instance()
    {
        static $instance;
        if (!($instance instanceof self)) {
            $instance = new self();
        }

        return $instance;
    }

    /**
     * @param Request|null $request
     * @return bool
     */
    public function dispatch(Request $request = null)
    {
        $request = $request ? $request : $this->getRequest();
        $this->_hops++;

        if ($this->_hops <= $this->_maxHops) {
            if ($request->getControllerName() == null) {
                $this->setErrorControler();

                return $this->dispatch();
            }

            $controller = $this->generateController($request);

            $profilerName = "dispatch [{$request->getMCA()}]";

            \Nip_Profiler::instance()->start($profilerName);
            if ($controller instanceof Controller) {
                try {
                    $this->_currentController = $controller;
                    $controller->setRequest($request);
                    $controller->dispatch();
                } catch (ForwardException $e) {
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
            trigger_error("Maximum number of hops ($this->_maxHops) has been reached for {$request->getMCA()}",
                E_USER_ERROR);
        }

        \Nip_Profiler::instance()->end($profilerName);

        return true;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    public function setErrorControler()
    {
        $this->getRequest()->setActionName('index');
        $this->getRequest()->setControllerName('error');
        $this->getRequest()->setModuleName('default');
    }

    /**
     * @param Request $request
     * @return Controller|null
     */
    public function generateController($request)
    {
        $controllerClass = $this->getFullControllerNameFromRequest($request);

        try {
            AutoLoader::instance()->load($controllerClass);
        } catch (AutoLoader\Exception $e) {
            $this->getFrontController()->getTrace()->add($e->getMessage());

            return null;
        }

        /* @var $controllerClass Controller */
        $controller = $this->newController($controllerClass);

        return $controller;
    }

    /**
     * @param Request $request
     * @return string
     */
    public function getFullControllerNameFromRequest($request)
    {
        $module = $this->formatModuleName($request->getModuleName());
        $controller = $this->formatControllerName($request->getControllerName());

        return $this->getFullControllerName($module, $controller);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function formatModuleName($name)
    {
        $name = $name ? $name : 'default';

        return inflector()->camelize($name);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function formatControllerName($name)
    {
        $name = $name ? $name : 'index';

        return $this->getControllerName($name);
    }

    public static function getControllerName($controller)
    {
        return inflector()->classify($controller);
    }

    /**
     * @param $module
     * @param $controller
     * @return string
     */
    public function getFullControllerName($module, $controller)
    {
        $namespaceClass = $this->generateFullControllerNameNamespace($module, $controller);
        $loader = $this->getFrontController()->getApplication()->getAutoloader()->getPsr4ClassLoader();
        $loader->load($namespaceClass);
        if ($loader->isLoaded($namespaceClass)) {
            return $namespaceClass;
        }

        return $this->generateFullControllerNameString($module, $controller);
    }

    /**
     * @param $module
     * @param $controller
     * @return string
     */
    protected function generateFullControllerNameNamespace($module, $controller)
    {
        $name = $this->getFrontController()->getApplication()->getRootNamespace().'Modules\\';
        $name .= $module.'\Controllers\\';
        $name .= str_replace('_', '\\', $controller)."Controller";

        return $name;
    }

    /**
     * @return FrontController
     */
    public function getFrontController()
    {
        if (!$this->_frontController) {
            $this->initFrontController();
        }

        return $this->_frontController;
    }

    /**
     * @param FrontController $controller
     * @return $this
     */
    public function setFrontController(FrontController $controller)
    {
        $this->_frontController = $controller;
        $this->request = $controller->getRequest();

        return $this;
    }

    public function initFrontController()
    {
        $this->_frontController = $this->newFrontController();
    }

    /**
     * @return FrontController
     */
    public function newFrontController()
    {
        return FrontController::instance();
    }

    /**
     * @param $module
     * @param $controller
     * @return string
     */
    protected function generateFullControllerNameString($module, $controller)
    {
        return $module."_".$controller."Controller";
    }

    public function newController($class)
    {
        $controller = new $class();
        /** @var Controller $controller */
        $controller->setDispatcher($this);

        return $controller;
    }

    public function throwError($params = false)
    {
        $this->getFrontController()->getTrace()->add($params);
        $this->setErrorControler();
        $this->forward('index');

        return;
    }

    public function forward($action = false, $controller = false, $module = false, $params = array())
    {
        $this->getRequest()->setActionName($action);

        if ($controller) {
            $this->getRequest()->setControllerName($controller);
        }
        if ($module) {
            $this->getRequest()->setModuleName($module);
        }

        if (is_array($params)) {
            $this->getRequest()->attributes->add($params);
        }

        throw new ForwardException;
    }

    /**
     * @return bool
     */
    public function getCurrentController()
    {
        return $this->_currentController;
    }
}