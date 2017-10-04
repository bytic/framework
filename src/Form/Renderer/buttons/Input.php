<?php
class Nip_Form_Renderer_Button_Input extends Nip_Form_Renderer_Button_Abstract
{
    public function generateItem()
    {
        $this->getItem()->setValue($this->getItem()->getLabel());
        $return = '<input ' . $this->renderAttributes() . ' />';
        return $return;
    }

    public function getItemAttribs()
    {
        $attribs = parent::getItemAttribs();
        $attribs[] = 'type';
        $attribs[] = 'value';
        return $attribs;
    }
}
