<?php

use Nip\Container\Container;

if (!function_exists('app')) {
    /**
     * Get the available container instance.
     *
     * @param string $make
     * @param array $parameters
     *
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

/**
 * @return \Nip\I18n\Translator
 */
function translator()
{
    return app('translator');
}

/**
 * @return \Nip\Database\Connection
 */
function db()
{
    return Container::getInstance()->get('db.connection');
}