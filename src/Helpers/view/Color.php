<?php

namespace Nip\Helpers\View;

class Color extends AbstractHelper
{
    public function __call($name, $arguments)
    {
        $helper = HelperBroker::get('Color');

        return call_user_func_array([$helper, $name], $arguments);
    }
}
