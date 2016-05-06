<?php

class Nip_Dispatcher
{
	protected $_frontController = false;
	protected $_request;

	protected $_module = "default";
	protected $_controller = "error";
	protected $_action = "index";

    protected $_currentController = false;

    protected $_hops = 0;
	protected $_maxHops = 30;

	public function dispatch($action = false, $controller = false, $module = false, $params = array())
	{
		$this->_hops++;

		if ($this->_hops <= $this->_maxHops) {
            $this->_module = $this->getRequest()->module = ($module ? $module : $this->_module);
            $this->_controller = $this->getRequest()->controller = ($controller ? $controller : $this->_controller);
            $this->_action = $this->getRequest()->action = ($action ? $action : $this->_action);
            
			list($controller, $action) = $this->prepareControllerAction($action, $controller, $module, $params);

            $profilerName = "dispatch [{$this->_module}.{$this->_controller}.{$this->_action}]";
            Nip_Profiler::instance()->start($profilerName);
			if ($controller instanceof Nip_Controller) {
				try {
                    $this->_currentController = $controller;
                    
					$controller->dispatch($action);
				} catch (Nip_Dispatcher_ForwardException $e) {
					$return = $this->dispatch();
                    Nip_Profiler::instance()->end($profilerName);
					return $return;
				}
			} else {
                $this->setErrorControler();
                $return = $this->dispatch();
                Nip_Profiler::instance()->end($profilerName);
                return $return;
			}
		} else {
			trigger_error("Maximum number of hops ($this->_maxHops) has been reached for {$this->_module}-{$this->_controller}-{$this->_action}", E_USER_ERROR);
		}

        Nip_Profiler::instance()->end($profilerName);
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
			$this->getRequest()->setParams($params);
		}

		throw new Nip_Dispatcher_ForwardException;
	}

	public function prepareControllerAction($action = false, $controller = false, $module = false, $params = array())
	{
		$module = $module ? $module : $this->_module;
		$controller = $controller ? $controller : $this->_controller;
		$action = $action ? $action : $this->_action;
        
		if ($params) {
			$this->getRequest()->setParams($params);
		}

		$controller = $this->getFullControllerName($module, $controller);
		$action = $this->formatActionName($action);

		try {
			Nip_AutoLoader::instance()->load($controller);
		} catch (Nip_AutoLoader_Exception $e) {
            Nip_FrontController::instance()->getTrace()->add($e->getMessage());
			return;
		}

		/* @var $controller Nip_Controller */
		$controller = new $controller();
		return array($controller, $action);
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
        Nip_FrontController::instance()->getTrace()->add($params);
        $this->setErrorControler();
		$this->forward('index');
		return;
	}

	public function reverseControllerName($controller)
	{
		return inflector()->unclassify($controller);
	}

	public function getControllerName($controller)
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
			$this->_frontController = Nip_FrontController::instance();
		}

		return $this->_frontController;
	}

    public function setFrontController($controller)
    {
        $this->_frontController = $controller;
        return $this;
    }

	public function getCurrentController()
    {
        return $this->_currentController;
    }

    /**
	 * @return Nip_Request
	 */
	public function getRequest()
	{
		if (!$this->_request) {
			$this->_request = Nip_Request::instance();
		}

		return $this->_request;
	}

	/**
	 * Singleton
	 * 
	 * @return Nip_Dispatcher
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
