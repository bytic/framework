<?php

use Nip_Form_Element_Checkbox as Checkbox;
use Nip_Form_Element_Input_Abstract as AbstractInput;

/**
 * Class Nip_Form_Renderer_Elements_Input_Group
 */
abstract class Nip_Form_Renderer_Elements_Input_Group extends Nip_Form_Renderer_Elements_Input_Abstract
{
    protected $_separator = '<br />';

    /** @noinspection PhpMissingParentCallCommonInspection
     * @return string
     */
    public function generateElement()
    {
        $elements = $this->getElement()->getElements();
        $returnElements = [];
        $return = '';
        foreach ($elements as $element) {
            $returnElements[] = $this->renderChildElement($element);
        }
        $return .= implode($this->getSeparator(), $returnElements);
        return $return;
    }

    /**
     * @param AbstractInput|Checkbox $element
     * @return mixed
     */
    public function renderChildElement($element)
    {
        if ($element->getValue() == $this->getElement()->getValue()) {
            $element->setChecked(true);
        }
        return $element->render();
    }

    /**
     * @return string
     */
    public function getSeparator()
    {
        return $this->_separator;
    }

    /**
     * @param $separator
     * @return $this
     */
    public function setSeparator($separator)
    {
        $this->_separator = $separator;
        return $this;
    }
}
