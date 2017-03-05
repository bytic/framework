<?php

namespace Nip\Router\Middleware;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Nip\Http\ServerMiddleware\Middlewares\ServerMiddlewareInterface;
use Nip\Router\Router;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class StartSession
 * @package Nip\Session\Middleware
 */
class RouteResolverMiddleware implements ServerMiddlewareInterface
{

    /**
     * The session manager.
     *
     * @var Router
     */
    protected $router;

    /**
     * Create a new session middleware.
     *
     * @param  Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * @inheritdoc
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $this->getRouter()->route($request);

        return $delegate->process($request);
    }


    /**
     * @return Router
     */
    public function getRouter(): Router
    {
        return $this->router;
    }
}
