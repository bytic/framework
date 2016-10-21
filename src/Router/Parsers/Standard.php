<?php

namespace Nip\Router\Parsers;

/**
 * Class Standard
 * @package Nip\Router\Parsers
 */
class Standard extends Dynamic
{

    protected $map = ':controller/:action';

    /**
     * @param array $params
     * @return mixed|string
     */
    public function assemble($params = [])
    {
        if (!$params['action']) {
            $params['action'] = '';
        }

        return parent::assemble($params);
    }

    /**
     * @param $uri
     * @return bool
     */
    public function match($uri)
    {
        $return = parent::match($uri);

        if ($return && !empty($this->getParam('controller'))) {
            return $return;
        }
        return false;
    }

    /** @noinspection PhpMissingParentCallCommonInspection
     * @return bool
     */
    protected function preMatch()
    {
        $mapCount = count($this->getParts());
        $uriCount = substr_count($this->uri, '/') + 1;
        $difference = $mapCount - $uriCount;
        if ($difference == 0 || $difference == 1) {
            return true;
        }

        return false;
    }
}
