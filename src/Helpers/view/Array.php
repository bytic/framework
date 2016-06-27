<?php
class Nip_Helper_View_Array extends Nip_Helper_View_Abstract {

    public function __call($name, $arguments) {
        $helper = Nip_Helper_Array::instance();

        return call_user_func_array(array($helper, $name), $arguments);
    }

}
