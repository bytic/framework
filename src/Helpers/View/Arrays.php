<?php

namespace Nip\Helpers\View;

class Arrays extends AbstractHelper
{

    public function __call($name, $arguments)
    {
        $helper = HelperBroker::get('Arrays');

        return call_user_func_array(array($helper, $name), $arguments);
    }

}
