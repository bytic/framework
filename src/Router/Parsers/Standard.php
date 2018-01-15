<?php

namespace Nip\Router\Parsers;

/**
 * Class Standard.
 */
class Standard extends Dynamic
{
    protected $map = ':controller/:action';

    /**
     * @param array $params
     *
     * @return mixed|string
     */
    public function assemble($params = [])
    {
        if (!$params['action']) {
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
        $uriCount = substr_count($this->uri, '/') + 1;
        $difference = $mapCount - $uriCount;
        if ($difference == 0 || $difference == 1) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
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
