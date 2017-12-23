<?php

namespace Nip\Helpers\View;

use Nip\HelperBroker;

/**
 * Class Arrays
 * @package Nip\Helpers\View
 */
class Arrays extends AbstractHelper
{
    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $helper = HelperBroker::get('Arrays');

        return call_user_func_array([$helper, $name], $arguments);
    }
}
