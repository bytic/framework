<?php
abstract class Nip_Form_Element_Input_Group extends Nip_Form_Element_Abstract
{
    protected $_type = 'input_group';
    protected $_elements = [];
    protected $_values = [];

    public function isGroup()
    {
        return true;
    }

    public function isRequestArray()
    {
        return false;
    }

    public function addOptionsArray($options, $valueKey, $labelKey)
    {
        foreach ($options as $key => $option) {
            $option = (object) $option;


            $oValue    = $option->$valueKey;
            $oLabel = $option->$labelKey;
            $oDisabled = $option->disabled;

            if ($oDisabled) {
                $atribs['disabled'] = 'disabled';
            }
            $this->addOption($oValue, $oLabel, $atribs);
        }

        return $this;
    }

    /**
     * @return Nip_Form_Element_Input_Group
     */
    public function addOption($value, $label, $attribs = array())
    {
        $element = $this->getNewElement();
        $element->setValue($value);
        $element->setLabel($label);
        $element->addAttribs($attribs);

        return $this->addElement($element);
    }

    /**
     * @return Nip_Form_Element_Abstract
     */
    public function getNewElement()
    {
        trigger_error('No new element funtion defined for this group', E_USER_ERROR);
    }

    public function addElement(Nip_Form_Element_Input_Abstract $element)
    {
        $key = $element->getValue();
        $this->_elements[$key] = $element;
        $this->_values[] = $key;
        return $this;
    }

    public function getElement($key)
    {
        return $this->_elements[$key];
    }

    public function getElements()
    {
        return $this->_elements;
    }

    public function getValues()
    {
        return $this->_values;
    }
}
