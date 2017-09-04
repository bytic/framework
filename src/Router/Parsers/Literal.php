<?php

namespace Nip\Router\Parsers;

/**
 * Class Literal
 * @package Nip\Router\Parsers
 */
class Literal extends AbstractParser
{

    /**
     * @param $uri
     * @return bool
     */
    public function match($uri)
    {
        $return = parent::match($uri);
        $map = rtrim($this->getMap(), '/');
        $uri = rtrim($uri, '/');

        return ($return) ? $map == $uri : false;
    }

    /** @noinspection PhpMissingParentCallCommonInspection
     * @param array $params
     * @return mixed|string
     */
    public function assemble($params = [])
    {
        $params = $this->stripEmptyParams($params);

        return $this->getMap() . ($params ? '?' . http_build_query($params) : '');
    }
}
