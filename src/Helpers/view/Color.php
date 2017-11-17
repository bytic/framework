<?php

namespace Nip\Helpers\View;

use Nip\HelperBroker;

/**
 * Class Color
 * @package Nip\Helpers\View
 */
class Color extends AbstractHelper
{
    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $helper = HelperBroker::get('Color');

        return call_user_func_array([$helper, $name], $arguments);
    }
}
