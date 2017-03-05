<?php

namespace Nip\Http\Response;

use Psr\Http\Message\ResponseInterface;

/**
 * Class ResponseFactory
 * @package Nip\Http\Response
 *
 * @inspiration https://github.com/laravel/framework/blob/master/src/Illuminate/Routing/ResponseFactory.php
 *
 */
class ResponseFactory
{
    /**
     * @param string $content
     * @param int $status
     * @param array $headers
     * @return ResponseInterface
     */
    public static function make($content = '', $status = 200, array $headers = [])
    {
        return new Response($content, $status, $headers);
    }
}
