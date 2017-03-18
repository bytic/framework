<?php

namespace Nip;

use Nip\Mvc\Sections\SectionsManager;

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
