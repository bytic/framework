<?php

use Nip\HelperBroker;

class Nip_Helper_View_URL extends Nip_Helper_View_Abstract
{

    public function __call($name, $arguments)
    {
        $helper = HelperBroker::get('Url');
        return call_user_func_array(array($helper, $name), $arguments);
    }

}
