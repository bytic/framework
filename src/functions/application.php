<?php

use Nip\Container\Container;

if (!function_exists('app')) {
    /**
     * Get the available container instance.
     *
     * @param  string $make
     * @param  array $parameters
     * @return mixed|Container
     */
    function app($make = null, $parameters = [])
    {
        if (is_null($make)) {
            return Container::getInstance();
        }

        return Container::getInstance()->get($make, $parameters);
    }
}

if (!function_exists('env')) {
    /**
     * Gets the value of an environment variable.
     *
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    function env($key, $default = null)
    {
        $value = getenv($key);
        if ($value === false) {
            return value($default);
        }
        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return;
        }
//        if (strlen($value) > 1 && Str::startsWith($value, '"') && Str::endsWith($value, '"')) {
//            return substr($value, 1, -1);
//        }
        return $value;
    }
}
if (!function_exists('config')) {
    /**
     * Get / set the specified configuration value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * @param  array|string $key
     * @param  mixed $default
     * @return \Nip\Config\Config|mixed
     */
    function config($key = null, $default = null)
    {
        if (is_null($key)) {
            return app('config');
        }
        if (is_array($key)) {
            return app('config')->set($key);
        }
        return app('config')->get($key, $default);
    }
}

if (!function_exists('request')) {
    /**
     * Get an instance of the current request or an input item from the request.
     *
     * @param  array|string $key
     * @param  mixed $default
     * @return Nip\Request|string|array
     */
    function request($key = null, $default = null)
    {
        $request = app('request');
        if (is_null($key)) {
            return $request;
        }
        $value = $request->get($key);
        return $value ? $value : $default;
    }
}

if (!function_exists('asset')) {
    /**
     * Generate an asset path for the application.
     *
     * @param  string $path
     * @param  bool $secure
     * @return string
     */
    function asset($path, $secure = null)
    {
        return app('url')->asset($path, $secure);
    }
}

/**
 * @return \Nip\I18n\Translator
 */
function translator()
{
    return app('translator');
}

/**
 * @return Nip\Inflector\Inflector
 */
function inflector()
{
    return app('inflector');
}
