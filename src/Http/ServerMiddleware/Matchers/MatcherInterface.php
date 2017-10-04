<?php

namespace Nip\Http\ServerMiddleware\Matchers;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface MatcherInterface
 * @package Nip\Http\ServerMiddleware\Matchers
 */
interface MatcherInterface
{
    /**
     * Evaluate if the request matches with the condition
     *
     * @param ServerRequestInterface $request
     *
     * @return bool
     */
    public function match(ServerRequestInterface $request): bool;
}
