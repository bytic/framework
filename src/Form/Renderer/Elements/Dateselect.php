<?php

class Nip_Form_Renderer_Elements_Dateselect extends Nip_Form_Renderer_Elements_MultiElement
{
    public function generateElement()
    {
        $return = '<div class="row">';

        $elements = $this->getElement()->getElements();
        foreach ($elements as $key => $element) {
            $element->addClass('form-control');
            $returnElements[] = '<div class="col-xs-4" style="max-width:' . ($key == 'day' ? 95 : 110) . 'px;">' .
                $element->render() . '</div>';
        }

        $return .= implode(' ', $returnElements);
        $return .= '</div>';
        return $return;
    }

    public function generateElement2()
    {
        if (!$this->getElement()->getAttrib('id')) {
            $this->getElement()->setAttrib('id', $this->getElement()->getJSID());
            $this->getElement()->addClass('datepicker');
        }
        $return = parent::generateElement();
        $return .= '<script type="text/javascript">';
        $return .= 'document.addEventListener("DOMContentLoaded", function() {';

        $options = [];
        $options[] = 'changeMonth: true';
        $options[] = 'changeYear: true';

        $yearRange = $this->getElement()->getOption('yearRange');
        if ($yearRange) {
            $options[] = 'yearRange: "' . $yearRange . '"';
        }
        $format = $this->getElement()->getFormat();
        $format = strtr($format, [
            'Y' => 'yy',
            'd' => 'dd',
            'm' => 'mm',
        ]);
        $options[] = 'dateFormat: "' . $format . '"';

        $return .= "    jQuery('#{$this->getElement()->getAttrib('id')}').datepicker({
			" . implode(',', $options) . "
		});";
        $return .= '});';
        $return .= '</script>';
        return $return;
    }
}
