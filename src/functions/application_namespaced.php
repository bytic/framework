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
