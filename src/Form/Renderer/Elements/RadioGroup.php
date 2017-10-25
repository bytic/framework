<?php

/**
 * Class Nip_Form_Renderer_Elements_RadioGroup
 */
class Nip_Form_Renderer_Elements_RadioGroup extends Nip_Form_Renderer_Elements_Input_Group
{

    /**
     * @return string
     */
    public function generateElement()
    {
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
