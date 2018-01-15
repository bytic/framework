<?php

class Nip_Form_Element_Input extends Nip_Form_Element_Input_Abstract
{
    public function init()
    {
        parent::init();
        $this->setAttrib('type', 'text');
    }
}
