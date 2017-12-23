<?php

class Nip_Form_Renderer_Elements_Radio extends Nip_Form_Renderer_Elements_Input_Abstract
{
    public function generateElement()
    {
        $this->getElement()->addClass('form-check-input');

        $class = get_class($this->getRenderer()) == Nip_Form_Renderer_Bootstrap::class ? 'radio' : 'form-check';
        $return = '<div class="'.$class.'">';
        $return .= '<label class="form-check-label">';
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
