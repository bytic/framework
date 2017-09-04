<?php

namespace Nip\Http\Request\Traits;

/**
 * Class InteractsWithMca
 * @package Nip\Http\Request\Traits
 */
trait InteractsWithMca
{
    /**
     * Has the action been dispatched?
     * @var boolean
     */
    protected $dispatched = false;

    /**
     * Module
     * @var string
     */
    protected $module;

    /**
     * Module key for retrieving module from params
     * @var string
     */
    protected $moduleKey = 'module';

    /**
     * Controller
     * @var string
     */
    protected $controller;

    /**
     * Controller key for retrieving controller from params
     * @var string
     */
    protected $controllerKey = 'controller';

    /**
     * Action
     * @var string
     */
    protected $action;

    /**
     * Action key for retrieving action from params
     * @var string
     */
    protected $actionKey = 'action';

    /**
     * Retrieve the action key
     *
     * @return string
     */
    public function getActionKey()
    {
        return $this->actionKey;
    }

    /**
     * Set the action key
     *
     * @param string $key
     * @return self
     */
    public function setActionKey($key)
    {
        $this->actionKey = (string) $key;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasMCA()
    {
        return $this->getMCA() != '..';
    }

    /**
     * @return string
     */
    public function getMCA()
    {
        return $this->getModuleName() . '.' . $this->getControllerName() . '.' . $this->getActionName();
    }

    /**
     * Retrieve the module name
     * @return string
     */
    public function getModuleName()
    {
        if (null === $this->module) {
            $this->module = $this->attributes->get($this->getModuleKey());
        }

        return $this->module;
    }

    /**
     * Retrieve the module key
     *
     * @return string
     */
    public function getModuleKey()
    {
        return $this->moduleKey;
    }

    /**
     * Set the module key
     *
     * @param string $key
     * @return $this
     */
    public function setModuleKey($key)
    {
        $this->moduleKey = (string) $key;

        return $this;
    }

    /**
     * Retrieve the controller name
     * @return string
     */
    public function getControllerName()
    {
        if ($this->controller === null) {
            $this->initControllerName();
        }

        return $this->controller;
    }

    public function initControllerName()
    {
        $this->controller = $this->attributes->get($this->getControllerKey());
    }

    /**
     * Retrieve the controller key
     *
     * @return string
     */
    public function getControllerKey()
    {
        return $this->controllerKey;
    }

    /**
     * Set the controller key
     *
     * @param string $key
     * @return self
     */
    public function setControllerKey($key)
    {
        $this->controllerKey = (string) $key;

        return $this;
    }

    /**
     * Retrieve the action name
     *
     * @return string
     */
    public function getActionName()
    {
        if (null === $this->action) {
            $this->setActionName($this->getActionDefault());
        }

        return $this->action;
    }

    /**
     * Set the action name
     * @param string $value
     * @return self
     */
    public function setActionName($value)
    {
        if ($value) {
            $this->action = $value;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getActionDefault()
    {
        return 'index';
    }

    /**
     * Set the controller name to use
     *
     * @param string $value
     * @return self
     */
    public function setControllerName($value)
    {
        if ($value) {
            $this->controller = $value;
        }

        return $this;
    }

    /**
     * Set the module name to use
     * @param string $value
     * @return self
     */
    public function setModuleName($value)
    {
        if ($value) {
            $this->module = $value;
        }

        return $this;
    }
}
