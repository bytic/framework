<?php

namespace Nip;

use Nip\AutoLoader\Exception;
use Nip\Mvc\Sections\SectionsManager;
use Nip\Records\RecordManager;

if (!function_exists('url')) {
    /**
     * Get Url Generator
     * @return \Nip\Router\Generator\UrlGenerator
     */
    function url()
    {
        return app('url');
    }
}

if (!function_exists('locale')) {
    /**
     * Get Locale
     * @return \Nip\Locale\Locale
     */
    function locale()
    {
        return app('locale');
    }
}

if (!function_exists('sections')) {
    /**
     * Get SectionsManager
     * @return SectionsManager
     */
    function sections()
    {
        return app('mvc.sections');
    }
}

if (!function_exists('storage_path')) {
    /**
     * Get the path to the storage folder.
     *
     * @param  string $path
     * @return string
     */
    function storage_path($path = '')
    {
        return app('path.storage').($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}

if (!function_exists('recordManager')) {
    /**
     * Returns the models manager class based on string
     *
     * @param string $model
     * @return RecordManager
     * @throws Exception
     */
    function recordManager($model)
    {
        $managerClass = app('app')->getRootNamespace().$model;
        if (class_exists($managerClass)) {
            return call_user_func([$managerClass, "instance"]);
        }
        if (class_exists($model)) {
            return call_user_func([$managerClass, "instance"]);
        }

        throw new Exception("Invalid record manager {$model}");
    }
}
