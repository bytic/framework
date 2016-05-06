<?php
abstract class Nip_Form_Renderer_Elements_Input_Group extends Nip_Form_Renderer_Elements_Input_Abstract {

    protected $_separator = '<br />';
    
    public function generateElement() {
        $elements = $this->getElement()->getElements();
        $return = '';
        foreach ($elements as $element) {
            if ($element->getValue() == $this->getElement()->getValue()) {
                $element->setChecked(true);
            }
            $returnElements[] = $element->render();
        }
        $return .= implode($this->getSeparator(), $returnElements);
        return $return;
    }

    public function getSeparator()
    {
        return $this->_separator;
    }

    public function setSeparator($separator)
    {
        $this->_separator = $separator;
        return $this;
    }
    
    
}