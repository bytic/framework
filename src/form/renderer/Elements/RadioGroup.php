<?php
class Nip_Form_Renderer_Elements_RadioGroup extends Nip_Form_Renderer_Elements_Input_Group {
    
    public function generateElement() {
        $this->_checkValue();

        return parent::generateElement();
    }
    
    protected function _checkValue()
    {
        if (!$this->getElement()->getValue()) {
            $elements = $this->getElement()->getElements();
            if ($elements) {
                $element = reset($elements);
                $this->getElement()->setValue($element->getValue());
            }
        }        
    }

}