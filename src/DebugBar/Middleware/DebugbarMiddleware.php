<?php

namespace Nip\DebugBar\Middleware;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Nip\DebugBar\DebugBar;
use Nip\Http\ServerMiddleware\Middlewares\ServerMiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class DebugbarMiddleware
 * @package Nip\DebugBar\Middleware
 */
class DebugbarMiddleware implements ServerMiddlewareInterface
{
    /**
     * The DebugBar instance
     *
     * @var DebugBar
     */
    protected $debugbar;

    /**
     * Create a new session middleware.
     *
     * @param  DebugBar $debugbar
     */
    public function __construct(DebugBar $debugbar)
    {
        $this->debugbar = $debugbar;
    }

    /**
     * @inheritdoc
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $response = $delegate->process($request);

        // Modify the response to add the Debugbar
        $this->debugbar->modifyResponse($request, $response);
        return $response;
    }
}
