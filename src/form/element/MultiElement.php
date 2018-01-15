<?php

class Nip_Form_Element_MultiElement extends Nip_Form_Element_Abstract
{
    protected $_type = 'multiElement';
    protected $_elements = [];

    public function addElement(Nip_Form_Element_Abstract $element)
    {
        $key = $element->getName();
        $this->_elements[$key] = $element;

        return $this;
    }

    public function getElements()
    {
        reset($this->_elements);

        return $this->_elements;
    }
}
