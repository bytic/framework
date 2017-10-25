<?php

class Nip_Form_Element_Select extends Nip_Form_Element_Abstract
{
    protected $_type = 'select';
    protected $_optionsElements = [];
    protected $_values = [];

    /**
     * @return Nip_Form_Element_Select
     */
    public function addOptionsArray($options, $valueKey, $labelKey)
    {
        foreach ($options as $key => $option) {
            $option = (object) $option;

            $oValue = $option->$valueKey;
            $oLabel = $option->$labelKey;
            $oDisabled = $option->disabled;

            $atribs = array(
                'label' => $oLabel,
            );

            if ($oDisabled) {
                $atribs['disabled'] = 'disabled';
            }
            $this->addOption($oValue, $atribs);
        }

        return $this;
    }

    /**
     * @return Nip_Form_Element_Select
     */
    public function addOption($value, $label)
    {
        if (is_array($label)) {
            $option = $label;
        } else {
            $option['label'] = $label;
        }

        $this->_optionsElements[$value] = $option;
        $this->_values[] = $value;

        return $this;
    }

    /**
     * @return Nip_Form_Element_Select
     */
    public function addOptions(array $array)
    {
        foreach ($array as $value => $label) {
            $this->addOption($value, $label);
        }

        return $this;
    }

    /**
     * @return Nip_Form_Element_Select
     */
    public function appendOptgroupOption($optgroup, $value, $label)
    {
        if (is_array($label)) {
            $option = $label;
        } else {
            $option['label'] = $label;
        }

        $this->_optionsElements[$optgroup][$value] = $option;
        $this->_values[] = $value;

        return $this;
    }

    /**
     * @deprecated to stop confusion from select options and element options
     * @return array
     */
    public function getOptions()
    {
        return $this->_optionsElements;
    }

    /**
     * @return array
     */
    public function getOptionsElements()
    {
        return $this->_optionsElements;
    }

    public function setValue($value)
    {
        if (in_array($value, $this->_values)) {
            return parent::setValue($value);
        }

        return false;
    }
}
