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
        if (!isset($params['action']) or !$params['action']) {
            $params['action'] = '';
        }

        return parent::assemble($params);
    }

    /** @noinspection PhpMissingParentCallCommonInspection
     * @return bool
     */
    protected function preMatch()
    {
        $mapCount = count($this->getParts());
        $uriCount = count($this->getUriParts());
        $difference = $mapCount - $uriCount;
        if ($difference == 0 || $difference == 1) {
            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    protected function postMatch()
    {
        if (parent::postMatch()) {
            return $this->validateController();
        }

        return false;
    }

    /**
     * @return bool
     */
    protected function validateController()
    {
        $controller = $this->getParam('controller');
        if (!empty($controller)) {
            return true;
        }

        return false;
    }
}
