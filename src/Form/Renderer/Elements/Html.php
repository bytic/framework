<?php
class Nip_Form_Renderer_Elements_Html extends Nip_Form_Renderer_Elements_Abstract
{
    public function generateElement()
    {
        $return = $this->getElement()->getValue();
        return $return;
    }
}
