<?php

namespace Nip;

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
