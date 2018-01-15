<?php

class Nip_Form_Element_Password extends Nip_Form_Element_Input_Abstract
{
    public function init()
    {
        parent::init();
        $this->setAttrib('type', 'password');
    }
}
