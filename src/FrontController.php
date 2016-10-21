<?php

namespace Nip;

use Nip\Dispatcher\DispatcherAwareTrait;
use Nip\Router\RouterAwareTrait;

/**
 * Class FrontController
 * @package Nip
 */
class FrontController
{
    use DispatcherAwareTrait;
    use RouterAwareTrait;

    /**
     * @var Application
     */
    protected $application;

    protected $request;

    protected $requestURI = null;

    /**
     * Singleton
     *
     * @return self
     */
    public static function instance()
    {
        static $instance;
        if (!($instance instanceof self)) {
            $instance = new self;
        }

        return $instance;
    }

    /**
     * @return Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * @param Application $bootstrap
     */
    public function setApplication($bootstrap)
    {
        $this->application = $bootstrap;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        if (!$this->request) {
            $this->request = new Request();
        }

        return $this->request;
    }

    /**
     * @param bool|Request $request
     * @return $this
     */
    public function setRequest($request = false)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * @return FrontController\Trace
     */
    public function getTrace()
    {
        return FrontController\Trace::instance();
    }
}
