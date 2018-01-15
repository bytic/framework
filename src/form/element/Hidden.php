<?php

class Nip_Form_Element_Hidden extends Nip_Form_Element_Input_Abstract
{
    protected $_type = 'hidden';

    public function init()
    {
        parent::init();
        $this->setAttrib('type', 'hidden');
    }

    public function getDataFromRequest($request)
    {
        if ($this->getOption('readRequest') === true) {
            return parent::getDataFromRequest($request);
        }

        return $this;
    }
}
