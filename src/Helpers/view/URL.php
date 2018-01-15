<?php

namespace Nip\Helpers\View;

use Nip\HelperBroker;

class URL extends AbstractHelper
{
    public function __call($name, $arguments)
    {
        $helper = HelperBroker::get('Url');

        return call_user_func_array([$helper, $name], $arguments);
    }
}
