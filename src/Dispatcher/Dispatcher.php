<?php

namespace Nip\Dispatcher;

use Nip\AutoLoader\AutoLoader;
use Nip\AutoLoader\Exception as AutoLoaderException;
use Nip\Controller;
use Nip\Request;

/**
 * Class Dispatcher
 * @package Nip\Dispatcher
 */
class Dispatcher
{

    /**
     * @var null|Request
     */
    protected $request = null;

    protected $currentController = false;

    protected $hops = 0;

    protected $maxHops = 30;

    /**
     * @param $controller
     * @return mixed
     */
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
     * @param Request|null $request
     * @return bool
     */
    public function dispatch(Request $request = null)
    {
        $request = $request ? $request : $this->getRequest();
        $this->hops++;

        if ($this->hops <= $this->maxHops) {
            if ($request->getControllerName() == null) {
                $this->setErrorControler();

                return $this->dispatch();
            }

            $controller = $this->generateController($request);

            $profilerName = "dispatch [{$request->getMCA()}]";

            \Nip_Profiler::instance()->start($profilerName);
            if ($controller instanceof Controller) {
                try {
                    $this->currentController = $controller;
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
            trigger_error("Maximum number of hops ($this->maxHops) has been reached for {$request->getMCA()}",
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
            $this->getAutoloader()->load($controllerClass);
        } catch (AutoLoaderException $e) {
//            $this->getFrontController()->getTrace()->add($e->getMessage());
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

    /**
     * @param $controller
     * @return mixed
     */
    public function getControllerName($controller)
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
        $loader = $this->getAutoloader()->getPsr4ClassLoader();
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
        $name = app()->get('kernel')->getRootNamespace().'Modules\\';
        $module = $module == 'Default' ? 'Frontend' : $module;
        $name .= $module.'\Controllers\\';
        $name .= str_replace('_', '\\', $controller)."Controller";

        return $name;
    }

    /**
     * @return AutoLoader
     */
    protected function getAutoloader()
    {
        return app('autoloader');
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

    /**
     * @param $class
     * @return Controller
     */
    public function newController($class)
    {
        $controller = new $class();
        /** @var Controller $controller */
        $controller->setDispatcher($this);

        return $controller;
    }

    /**
     * @param bool $params
     */
    public function throwError($params = false)
    {
//        $this->getFrontController()->getTrace()->add($params);
        $this->setErrorControler();
        $this->forward('index');

        return;
    }

    /**
     * @param bool $action
     * @param bool $controller
     * @param bool $module
     * @param array $params
     * @throws ForwardException
     */
    public function forward($action = false, $controller = false, $module = false, $params = [])
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
        return $this->currentController;
    }
}
