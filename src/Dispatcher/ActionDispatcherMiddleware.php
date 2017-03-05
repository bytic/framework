<?php

namespace Nip\Dispatcher;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Nip\Http\ServerMiddleware\Middlewares\ServerMiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class ActionDispatcherMiddleware
 */
class ActionDispatcherMiddleware implements ServerMiddlewareInterface
{

    /**
     * The session manager.
     *
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * Create a new session middleware.
     *
     * @param  Dispatcher $dispatcher
     */
    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @inheritdoc
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        return $this->getDispatcher()->dispatch($request);
    }

    /**
     * @return Dispatcher
     */
    protected function getDispatcher(): Dispatcher
    {
        return $this->dispatcher;
    }
}
