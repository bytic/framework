<?php

class Nip_Form_Button_Input extends Nip_Form_Button_Abstract
{
    protected $_type = 'input';

    public function init()
    {
        parent::init();
        $this->setAttrib('type', 'submit');
    }
}
