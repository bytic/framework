<?php

namespace Nip\Router\Generator;

use Nip\Request;
use Nip\Router\RouteCollection;
use Nip\Utility\Str;

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
     * A cached copy of the URL root for the current request.
     *
     * @var string|null
     */
    protected $cachedRoot;

    /**
     * A cached copy of the URL schema for the current request.
     *
     * @var string|null
     */
    protected $cachedSchema;

    /**
     * The forced URL root.
     *
     * @var string
     */
    protected $forcedRoot;

    /**
     * The forced schema for URLs.
     *
     * @var string
     */
    protected $forceScheme;

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

        $this->cachedRoot = null;
        $this->cachedSchema = null;
//        $this->routeGenerator = null;
    }

    /**
     * Generate the URL to an application asset.
     *
     * @param  string $path
     * @param  bool|null $secure
     * @return string
     */
    public function asset($path, $secure = null)
    {
        if ($this->isValidUrl($path)) {
            return $path;
        }
        // Once we get the root URL, we will check to see if it contains an index.php
        // file in the paths. If it does, we will remove it since it is not needed
        // for asset paths, but only for routes to endpoints in the application.
        $root = $this->formatRoot($this->formatScheme($secure));
        return $this->removeIndex($root) . '/assets/' . trim($path, '/');
    }

    /**
     * Determine if the given path is a valid URL.
     *
     * @param  string $path
     * @return bool
     */
    public function isValidUrl($path)
    {
        if (!Str::startsWith($path, ['#', '//', 'mailto:', 'tel:', 'http://', 'https://'])) {
            return filter_var($path, FILTER_VALIDATE_URL) !== false;
        }
        return true;
    }

    /**
     * Get the base URL for the request.
     *
     * @param  string $scheme
     * @param  string $root
     * @return string
     */
    public function formatRoot($scheme, $root = null)
    {
        if (is_null($root)) {
            if (is_null($this->cachedRoot)) {
                $this->cachedRoot = $this->forcedRoot ?: $this->request->root();
            }
            $root = $this->cachedRoot;
        }
        $start = Str::startsWith($root, 'http://') ? 'http://' : 'https://';
        return preg_replace('~' . $start . '~', $scheme, $root, 1);
    }

    /**
     * Get the default scheme for a raw URL.
     *
     * @param  bool|null $secure
     * @return string
     */
    public function formatScheme($secure)
    {
        if (!is_null($secure)) {
            return $secure ? 'https://' : 'http://';
        }
        if (is_null($this->cachedSchema)) {
            $this->cachedSchema = $this->forceScheme ?: $this->request->getScheme() . '://';
        }
        return $this->cachedSchema;
    }

    /**
     * Remove the index.php file from a path.
     *
     * @param  string $root
     * @return string
     */
    protected function removeIndex($root)
    {
        $i = 'index.php';
        return Str::contains($root, $i) ? str_replace('/' . $i, '', $root) : $root;
    }
}
