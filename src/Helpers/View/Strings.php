<?php

namespace Nip\Helpers\View;

use Nip\HelperBroker;

class Strings extends AbstractHelper
{

    public function __call($name, $arguments)
    {
        $helper = HelperBroker::get('String');

        return call_user_func_array(array($helper, $name), $arguments);
    }

}
