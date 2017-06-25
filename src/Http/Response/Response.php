<?php

namespace Nip\Http\Response;

use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

/**
 * Class Response
 * @package Nip\Http\Response
 */
class Response extends BaseResponse implements ResponseInterface
{
    use PsrBridgeTrait;
}
