<?php
class Nip_Form_Element_RadioGroup extends Nip_Form_Element_Input_Group
{
    protected $_type = 'radioGroup';

    public function getNewElement()
    {
        $element = $this->getForm()->getNewElement('radio');
        $element->setName($this->getName());
        return $element;
    }

    public function setValue($value)
    {
        $elements = $this->getElements();
        foreach ($elements as $element) {
            if ($element->getValue() == $value) {
                $element->setChecked(true);
                break;
            }
        }

        return parent::setValue($value);
    }
}
