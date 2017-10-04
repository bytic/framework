<?php
class Nip_Form_Element_CheckboxGroup extends Nip_Form_Element_Input_Group
{
    protected $_type = 'checkboxGroup';

    /**
     * @param string $requester
     * @return null
     */
    public function getValue($requester = 'abstract')
    {
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

    /**
     * @param $value
     * @return $this
     */
    public function setValue($value)
    {
        return $this->getDataFromRequest($value);
    }

    /**
     * @param $request
     * @return $this
     */
    public function getDataFromRequest($request)
    {
        $elements = $this->getElements();
        if (is_array($request)) {
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

    /**
     * @return Nip_Form_Element_Abstract
     */
    public function getNewElement()
    {
        $element = $this->getForm()->getNewElement('checkbox');
        $name = $this->getName();
        if (!strpos($name, '[]')) {
            $name = $name . '[]';
        }
        $element->setName($name);
        return $element;
    }

    /**
     * @return bool
     */
    public function isRequestArray()
    {
        return true;
    }
}
