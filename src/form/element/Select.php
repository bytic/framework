<?php
class Nip_Form_Element_Select extends Nip_Form_Element_Abstract {

    protected $_type = 'select';

    protected $_valueOptions = array();
    protected $_values = array();

    /**
     * @return Nip_Form_Element_Select
     */
    public function addOptionsArray($options, $valueKey, $labelKey) {
        foreach ($options as $key => $option) {
            $option = (object) $option;
            
            $oValue    = $option->$valueKey;
            $oLabel   = $option->$labelKey;
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
    public function addOptions(array $array) {
        foreach ($array as $value => $label) {
            $this->addOption($value, $label);
        }

        return $this;
    }

    /**
     * @return Nip_Form_Element_Select
     */
    public function addOption($value, $label) {
        if (is_array($label)) {
            $option = $label;
        } else {
            $option['label'] = $label;
        }
        
        $this->_valueOptions[$value] = $option;
        $this->_values[] = $value;

        return $this;
    }

    /**
     * @return Nip_Form_Element_Select
     */
    public function appendOptgroupOption($optgroup, $value, $label) {
        if (is_array($label)) {
            $option = $label;
        } else {
            $option['label'] = $label;
        }

        $this->_valueOptions[$optgroup][$value] = $option;
        $this->_values[] = $value;

        return $this;
    }

    public function getOptions() {
        return $this->_valueOptions;
    }

    public function setValue($value) {

        if (in_array($value, $this->_values)) {
            return parent::setValue($value);
        }
        return false;
    }
    
}
