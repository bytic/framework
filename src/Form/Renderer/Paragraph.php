<?php

use Nip\Form\Renderer\AbstractRenderer;

class Nip_Form_Renderer_Paragraph extends AbstractRenderer
{
    public function renderElements()
    {
        $return = '';

        $renderRows = $this->renderRows();
        if ($renderRows) {
            $return .= $renderRows;
        }

        return $return;
    }

    public function renderRows()
    {
        $elements = $this->getElements();
        $return = '';
        foreach ($elements as $element) {
            $return .= $this->renderRow($element);
        }

        return $return;
    }

    public function renderRow($element)
    {
        $return = '';
        if (!$element->isRendered()) {
            $return .= '<p class="row row-'.$element->getUniqueId().($element->isError() ? ' error' : '').'">';

            $return .= $this->renderLabel($element);

            $class = "value ".($element->getType() == 'input' ? 'input' : '');
            $return .= '<span class="'.$class.'">';
            $return .= $element->renderElement();
            $return .= '</span>';

            $return .= $element->renderErrors();

            $return .= '</p>';
        }

        return $return;
    }
}
