<?php

namespace Nip\Http\ServerMiddleware\Traits;

use Nip\Http\Response\Response;
use Nip\Request;

/**
 * Class HasServerMiddleware
 * @package Nip\Http\ServerMiddleware
 */
trait HasServerMiddleware
{

    /**
     * The application's middleware stack.
     *
     * @var array
     */
    protected $middleware = [];


    /**
     * Determine if the kernel has a given middleware.
     *
     * @param  string $middleware
     * @return bool
     */
    public function hasMiddleware($middleware)
    {
        return in_array($middleware, $this->middleware);
    }

    /**
     * Add a new middleware to beginning of the stack if it does not already exist.
     *
     * @param  string $middleware
     * @return $this
     */
    public function prependMiddleware($middleware)
    {
        if (array_search($middleware, $this->middleware) === false) {
            array_unshift($this->middleware, $middleware);
        }
        return $this;
    }

    /**
     * Add a new middleware to end of the stack if it does not already exist.
     *
     * @param  string $middleware
     * @return $this
     */
    public function pushMiddleware($middleware)
    {
        if (array_search($middleware, $this->middleware) === false) {
            $this->middleware[] = $middleware;
        }
        return $this;
    }


    /**
     * Call the terminate method on any terminable middleware.
     *
     * @param  Request $request
     * @param  Response $response
     * @return void
     */
    protected function terminateMiddleware($request, $response)
    {
    }
}
