<?php

class Nip_Form_Element_MultiElement extends Nip_Form_Element_Abstract
{
    protected $_type = 'multiElement';

    /**
     * @var Nip_Form_Element_Abstract[]
     */
    protected $elements = [];

    /**
     * @param Nip_Form_Element_Abstract $element
     * @return $this
     */
    public function addElement(Nip_Form_Element_Abstract $element)
    {
        $key = $element->getName();
        $this->elements[$key] = $element;
        return $this;
    }

    /**
     * @return Nip_Form_Element_Abstract[]
     */
    public function getElements()
    {
        reset($this->elements);
        return $this->elements;
    }

    /**
     * @param $name
     * @return Nip_Form_Element_Abstract
     * @throws \Nip\Logger\Exception
     */
    public function getElement($name)
    {
        if ($this->hasElement($name)) {
            return $this->elements[$name];
        }
        throw new \Nip\Logger\Exception("Invalid child element");
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasElement($name)
    {
        return isset($this->elements[$name]);
    }
}
