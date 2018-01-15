<?php

class Nip_Form_Renderer_Elements_Radio extends Nip_Form_Renderer_Elements_Input_Abstract
{
    public function generateElement()
    {
        $return = '<div class="radio">';
        $return .= '<label>';
        $return .= parent::generateElement();
        $return .= $this->getElement()->getLabel();
        $return .= '</label>';
        $return .= '</div>';

        return $return;
    }

    public function renderInput()
    {
        return parent::generateElement();
    }

    public function getelementattribs()
    {
        $attribs = parent::getelementattribs();
        $attribs[] = 'checked';

        return $attribs;
    }
}
