<?php

namespace Nip\Http\ServerMiddleware\Matchers;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Pattern
 * @package Nip\Http\ServerMiddleware\Matchers
 */
class Pattern implements MatcherInterface
{
    private $pattern;
    private $flags;
    private $result = true;

    /**
     * @param string $pattern
     * @param int $flags
     */
    public function __construct(string $pattern, $flags = 0)
    {
        if ($pattern[0] === '!') {
            $this->result = false;
            $pattern = substr($pattern, 1);
        }
        $this->pattern = $pattern;
        $this->flags = $flags;
    }

    /**
     * {@inheritdoc}
     */
    public function match(ServerRequestInterface $request): bool
    {
        return fnmatch($this->pattern, $request->getUri()->getPath(), $this->flags) === $this->result;
    }
}
