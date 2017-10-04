<?php

namespace Nip\Helpers\View;

use Nip\HelperBroker;

class Strings extends AbstractHelper
{
    public function __call($name, $arguments)
    {
        $helper = HelperBroker::get('Strings');

        return call_user_func_array(array($helper, $name), $arguments);
    }
}
