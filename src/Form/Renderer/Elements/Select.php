<?php
class Nip_Form_Renderer_Elements_Select extends Nip_Form_Renderer_Elements_Abstract
{
    public function generateElement()
    {
        $return = '<select ';
        $return .= $this->renderAttributes();
        $return .= ' >' . $this->renderOptions() . '</select>';
        return $return;
    }

    public function renderOptions($options = false)
    {
        $options = $options ? $options : $this->getElement()->getOptions();
        $return = '';
        foreach ($options as $value=>$atribs) {
            if (is_string($value) && !isset($atribs['label'])) {
                $return .= '<optgroup label="' . $value . '">';
                $return .= $this->renderOptions($atribs);
                $return .= '</optgroup>';
            } else {
                $return .= '<option';

                $label = $atribs['label'];
                unset($atribs['label']);

                $atribs['value'] = $value;
                $selectedValue = $this->getElement()->getValue();
                if ($selectedValue === 0 or $value === 0) {
                    if ($value === $selectedValue) {
                        $atribs['selected'] = 'selected';
                    }
                } elseif ($this->getElement()->getValue() == $value) {
                    $atribs['selected'] = 'selected';
                }
                
                foreach ($atribs as $name=>$value) {
                    $return .= ' ' . $name . '="' . $value . '"';
                }
                $return .= '>' . $label . '</option>';
            }
        }
        return $return;
    }

    public function getElementAttribs()
    {
        $attribs = parent::getElementAttribs();
        return $attribs;
    }
}
