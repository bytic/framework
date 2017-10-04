<?php
class Nip_Form_Renderer_Basic extends Nip_Form_Renderer_Table
{
    public function renderElements()
    {
        $elements = $this->getElements();
        if ($elements) {
            foreach ($elements as $element) {
                if (!$element->isRendered()) {
                    $idRow = $element->getUniqueId();
                    $this->setRowAttrib($idRow, 'class', "row " . $idRow);
                    $this->addCell($idRow, 1, $element, 'label');
                    $this->addCell($idRow, 2, $element, 'value');
                }
            }
        }
        return parent::renderElements();
    }
}
