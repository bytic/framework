<?php

namespace Nip\Dispatcher;

use Nip\Http\Response\Response;
use Nip\Request;

/**
 * Class ConfigAwareTrait
 * @package Nip\Config
 */
trait DispatcherAwareTrait
{
    /**
     * @var Dispatcher|null
     */
    protected $dispatcher = null;

    /**
     * @param Request|null $request
     * @return Response|null
     */
    public function dispatchRequest(Request $request = null)
    {
        return $this->getDispatcher()->dispatch($request);
    }

    /**
     * @return Dispatcher
     */
    public function getDispatcher()
    {
        if (!$this->dispatcher) {
            $this->initDispatcher();
        }

        return $this->dispatcher;
    }

    /**
     * @param Dispatcher $dispatcher
     * @return $this
     */
    public function setDispatcher($dispatcher = false)
    {
        $this->dispatcher = $dispatcher;

        return $this;
    }

    protected function initDispatcher()
    {
        $this->setDispatcher($this->newDispatcher());
    }

    /**
     * @return Dispatcher
     */
    protected function newDispatcher()
    {
        return app()->get('dispatcher');
    }
}
