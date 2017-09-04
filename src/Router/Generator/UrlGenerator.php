<?php

namespace Nip\Router\Generator;

use Nip\Request;
use Nip\Router\RouteCollection;
use Nip\Utility\Arr;
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
     * The callback to use to format hosts.
     *
     * @var \Closure
     */
    protected $formatHostUsing;

    /**
     * The callback to use to format paths.
     *
     * @var \Closure
     */
    protected $formatPathUsing;

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
     * Get the full URL for the current request.
     *
     * @return string
     */
    public function full()
    {
        return $this->request->fullUrl();
    }

    /**
     * Get the current URL for the request.
     *
     * @return string
     */
    public function current()
    {
        return $this->to($this->request->getPathInfo());
    }

    /**
     * Generate an absolute URL to the given path.
     *
     * @param  string  $path
     * @param  mixed  $extra
     * @param  bool|null  $secure
     * @return string
     */
    public function to($path, $extra = [], $secure = null)
    {
        // First we will check if the URL is already a valid URL. If it is we will not
        // try to generate a new one but will simply return the URL as is, which is
        // convenient since developers do not always have to check if it's valid.
        if ($this->isValidUrl($path)) {
            return $path;
        }
        $tail = implode('/', array_map(
                'rawurlencode', (array) $this->formatParameters($extra))
        );
        // Once we have the scheme we will compile the "tail" by collapsing the values
        // into a single string delimited by slashes. This just makes it convenient
        // for passing the array of parameters to this URL as a list of segments.
        $root = $this->formatRoot($this->formatScheme($secure));
        list($path, $query) = $this->extractQueryString($path);
        return $this->format(
                $root, '/' . trim($path . '/' . $tail, '/')
            ) . $query;
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
     * Format the array of URL parameters.
     *
     * @param  mixed|array  $parameters
     * @return array
     */
    public function formatParameters($parameters)
    {
        $parameters = Arr::wrap($parameters);
        foreach ($parameters as $key => $parameter) {
//            if ($parameter instanceof UrlRoutable) {
//                $parameters[$key] = $parameter->getRouteKey();
//            }
        }
        return $parameters;
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
     * Extract the query string from the given path.
     *
     * @param  string  $path
     * @return string[]
     */
    protected function extractQueryString($path)
    {
        if (($queryPosition = strpos($path, '?')) !== false) {
            return [
                substr($path, 0, $queryPosition),
                substr($path, $queryPosition),
            ];
        }
        return [$path, ''];
    }

    /**
     * Format the given URL segments into a single URL.
     *
     * @param  string $root
     * @param  string $path
     * @return string
     */
    public function format($root, $path)
    {
        $path = '/' . trim($path, '/');
        if ($this->formatHostUsing) {
            $root = call_user_func($this->formatHostUsing, $root);
        }
        if ($this->formatPathUsing) {
            $path = call_user_func($this->formatPathUsing, $path);
        }
        return trim($root . $path, '/');
    }

    /**
     * Get the URL for the previous request.
     *
     * @param  mixed $fallback
     * @return string
     */
    public function previous($fallback = false)
    {
        $referrer = $this->request->headers->get('referer');
        $url = $referrer ? $this->to($referrer) : $this->getPreviousUrlFromSession();
        if ($url) {
            return $url;
        } elseif ($fallback) {
            return $this->to($fallback);
        } else {
            return $this->to('/');
        }
    }

    /**
     * Get the previous URL from the session if possible.
     *
     * @return string|null
     */
    protected function getPreviousUrlFromSession()
    {
        return null;
//        $session = $this->getSession();
//        return $session ? $session->previousUrl() : null;
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
