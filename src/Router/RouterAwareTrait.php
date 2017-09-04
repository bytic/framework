<?php

namespace Nip\Router;

use Nip\Request;

/**
 * Class ConfigAwareTrait
 * @package Nip\Router
 */
trait RouterAwareTrait
{
    /**
     * @var Router|null
     */
    protected $router = null;

    /**
     * @param bool|Request $request
     * @return array
     */
    public function route($request = false)
    {
        $request = $request ? $request : $this->getRequest();
        $params = $this->getRouter()->route($request);

        return $params;
    }

    /**
     * @return Router
     */
    public function getRouter()
    {
        if (!$this->router) {
            $this->initRouter();
        }

        return $this->router;
    }

    /**
     * @param Router $router
     * @return $this
     */
    public function setRouter($router = false)
    {
        $this->router = $router;

        return $this;
    }

    protected function initRouter()
    {
        $this->setRouter($this->newRouter());
    }

    /**
     * @return Router
     */
    protected function newRouter()
    {
        return app()->get('router');
    }
}
