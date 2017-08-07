<?php

namespace Nip\Http\Response;

use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\JsonResponse as BaseJsonResponse;

/**
 * Class JsonResponse
 * @package Nip\Http\Response
 */
class JsonResponse extends BaseJsonResponse implements ResponseInterface
{
    use PsrBridgeTrait;
}
