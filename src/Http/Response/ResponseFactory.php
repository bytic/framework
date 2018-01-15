<?php

namespace Nip\Http\Response;

/**
 * Class ResponseFactory.
 *
 * @inspiration https://github.com/laravel/framework/blob/master/src/Illuminate/Routing/ResponseFactory.php
 */
class ResponseFactory
{
    /**
     * @param string $content
     * @param int    $status
     * @param array  $headers
     *
     * @return Response
     */
    public static function make($content = '', $status = 200, array $headers = [])
    {
        return new Response($content, $status, $headers);
    }
}
