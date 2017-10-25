<?php
class Nip_Form_Renderer_Button_Button extends Nip_Form_Renderer_Button_Abstract
{
    public function generateItem()
    {
        $return = '<button ' . $this->renderAttributes() . '>
                ' . $this->getItem()->getLabel() . '
            </button>';
        return $return;
    }
    
    public function getItemAttribs()
    {
        $attribs = parent::getItemAttribs();
        $attribs[] = 'type';
        return $attribs;
    }
}
