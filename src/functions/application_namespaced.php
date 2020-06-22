<?php

namespace Nip;

use Nip\AutoLoader\Exception;
use Nip\Locale\Locale;
use Nip\Mvc\Sections\SectionsManager;
use Nip\Records\RecordManager;

if (!function_exists('locale')) {
    /**
     * Get Locale
     * @return Locale
     */
    function locale()
    {
        return app('locale');
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
        $managerClassNamespaced = app('app')->getRootNamespace().'Models\\'.$model.'\\'.$model;
        if (class_exists($managerClassNamespaced)) {
            return call_user_func([$managerClassNamespaced, "instance"]);
        }
        if (class_exists($model)) {
            return call_user_func([$model, "instance"]);
        }

        throw new Exception("Invalid record manager [{$model}][{$managerClassNamespaced}]");
    }
}
