<?php
class Nip_Form_Renderer_Elements_MultiElement extends Nip_Form_Renderer_Elements_Input_Abstract
{
    public function generateElement()
    {
        $elements = $this->getElement()->getElements();
        $return = '';
        foreach ($elements as $element) {
            $returnElements[] = $element->render();
        }
        
        $return .= implode(' ', $returnElements);
        return $return;
    }
}
