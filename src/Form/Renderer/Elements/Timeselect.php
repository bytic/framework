<?php
class Nip_Form_Renderer_Elements_Timeselect extends Nip_Form_Renderer_Elements_MultiElement
{
    public function generateElement()
    {
        $return = '<div class="row">';
        
        $elements = $this->getElement()->getElements();
        foreach ($elements as $key=>$element) {
            $element->addClass('form-control');
            $returnElements[] =
                    '<div class="col-xs-4" style="max-width: 100px;">' .
                        $element->render() .
                    '</div>';
        }
        
        $return .= implode('', $returnElements);
        $return .= '</div>';
        return $return;
    }
}
