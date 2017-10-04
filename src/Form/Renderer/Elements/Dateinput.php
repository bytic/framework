<?php

class Nip_Form_Renderer_Elements_Dateinput extends Nip_Form_Renderer_Elements_Input
{
    public function generateElement()
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
