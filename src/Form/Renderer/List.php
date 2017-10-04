<?php

use Nip\Form\Renderer\AbstractRenderer;

class Nip_Form_Renderer_List extends AbstractRenderer
{
    protected $_list = [];

    public function setListAttrib($type, $value)
    {
        $this->_list[$type] = $value;

        return $this;
    }

    public function addClassName($name)
    {
        $this->_list['class'] .= ' '.$name;

        return $this;
    }

    public function renderElements()
    {
        $return = '<ul';
        foreach ($this->_list as $attrib => $value) {
            $return .= ' '.$attrib.'="'.$value.'"';
        }
        $return .= '>';

        $renderRows = $this->renderRows();
        if ($renderRows) {
            $return .= $renderRows;
        }
        $return .= '</ul>';

        return $return;
    }

    public function renderRows()
    {
        $elements = $this->getElements();
        $return = '';
        foreach ($elements as $element) {
            if (!$element->isRendered()) {
                $return .= '<li class="row">';

                $return = $this->renderLabel($element);

                $class = "value ".($element->getType() == 'input' ? 'input' : '');
                $return .= '<span class="'.$class.'">';
                $return .= $element->renderElement();
                $return .= '</span>';

                $return .= $element->renderErrors();

                $return .= '</li>';
            }
        }

        return $return;
    }
}
