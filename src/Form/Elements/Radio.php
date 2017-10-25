<?php
class Nip_Form_Element_Radio extends Nip_Form_Element_Input_Abstract
{
    protected $_type = 'radio';

    public function init()
    {
        parent::init();
        $this->setAttrib('type', 'radio');
    }

    public function isChecked()
    {
        return $this->getAttrib('checked') == 'checked';
    }


    public function setChecked($checked)
    {
        if ($checked === true) {
            $this->setAttrib('checked', 'checked');
        } else {
            $this->delAttrib('checked');
        }

        return $this;
    }
}
