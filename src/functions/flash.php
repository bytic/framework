<?php

/**
 * @param $name
 * @return mixed|object
 */
function flash_get($name)
{
    return app('flash.data')->get($name);
}

/**
 * @param $name
 * @param $value
 */
function flash_add($name, $value)
{
    app('flash.data')->add($name, $value);
}

/**
 * @param $name
 * @param $message
 */
function flash_success($name, $message)
{
    app('flash.messages')->add($name, 'success', $message);
}

/**
 * @param $name
 * @param $message
 */
function flash_error($name, $message)
{
    app('flash.messages')->add($name, 'error', $message);
}

/**
 * @param $name
 * @param $message
 */
function flash_info($name, $message)
{
    app('flash.messages')->add($name, 'info', $message);
}
