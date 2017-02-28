<?php

namespace Nip\Router\Generator;

use Nip\Request;
use Nip\Router\RouteCollection;

/**
 * Class UrlGenerator
 * @package Nip\Router\Generator
 */
class UrlGenerator
{

    /**
     * The route collection.
     *
     * @var RouteCollection
     */
    protected $routes;

    /**
     * The request instance.
     *
     * @var Request
     */
    protected $request;

    /**
     * Create a new URL Generator instance.
     *
     * @param  RouteCollection $routes
     * @param  Request $request
     */
    public function __construct(RouteCollection $routes, Request $request)
    {
        $this->routes = $routes;
        $this->setRequest($request);
    }


    /**
     * Set the current request instance.
     *
     * @param  Request $request
     * @return void
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

//        $this->cachedRoot = null;
//        $this->cachedSchema = null;
//        $this->routeGenerator = null;
    }
}

