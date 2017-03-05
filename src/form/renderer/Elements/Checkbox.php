<?php
class Nip_Form_Renderer_Elements_Checkbox extends Nip_Form_Renderer_Elements_Input_Abstract {

    public function generateElement() {
		if (!$this->getElement()->getValue()) {
			$this->getElement()->setValue('on');
		}
        $this->getElement()->removeClass('form-control');
        
        $return = '<div class="checkbox">';
            $return .= '<label>';
            $return .= parent::generateElement();
            $return .= ' '. $this->getElement()->getLabel();
            $return .= '</label>';
        $return .= '</div>';
        return $return;
    }
	
    public function getelementattribs() {
        $attribs = parent::getelementattribs();
        $attribs[] = 'checked';
        return $attribs;
    }

}