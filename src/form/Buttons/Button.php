<?php

class Nip_Form_Button_Button extends Nip_Form_Button_Abstract
{
    protected $_type = 'button';

    public function init()
    {
        parent::init();
        $this->setAttrib('type', 'submit');
    }
}
