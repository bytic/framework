<?php
class Nip_Helper_View_URL extends Nip_Helper_View_Abstract
{

    public function __call($name, $arguments)
    {
        $helper = Nip_Helper_URL::instance();
        return call_user_func_array(array($helper, $name), $arguments);
    }

}
