<?php
class Nip_Form_Element_CheckboxGroup extends Nip_Form_Element_Input_Group {

    protected $_type = 'checkboxGroup';

    public function getValue($requester = 'abstract') {
        $elements = $this->getElements();
        $data = [];
        if ($elements) {
            foreach ($elements as $element) {
                if ($element->isChecked()) {
                    $data[] = $element->getValue();
                }
            }
        }
        return $data;
    }

    public function setValue($value) {
        return $this->getDataFromRequest($value);
    }

    public function getDataFromRequest($request) {
//        var_dump($request);
        if (is_array($request)) {
            $elements = $this->getElements();
            foreach ($elements as $key=>$element) {
                $element->setChecked(in_array($key, $request));
            }
        } else {
            foreach ($elements as $key=>$element) {
                $element->setChecked(false);
            }
        }
        return $this;
    }

    public function getNewElement() {
        $element = $this->getForm()->getNewElement('checkbox');
        $name = $this->getName();
        if (!strpos($name, '[]')) {
            $name = $name .'[]';
        }
        $element->setName($name);
        return $element;
    }

    public function isRequestArray() {
        return true;
    }

}