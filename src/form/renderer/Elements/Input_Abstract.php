<?php

abstract class Nip_Form_Renderer_Elements_Input_Abstract extends Nip_Form_Renderer_Elements_Abstract
{
    public function generateElement()
    {
        $return = '<input ';
        $return .= $this->renderAttributes();
        $return .= ' />';

        return $return;
    }

    public function getElementAttribs()
    {
        $attribs = parent::getElementAttribs();
        $attribs[] = 'type';
        $attribs[] = 'value';
        $attribs[] = 'placeholder';
        $attribs[] = 'size';

        return $attribs;
    }
}
