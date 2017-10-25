<?php

class Nip_Form_Element_Checkbox extends Nip_Form_Element_Input_Abstract
{
    protected $_type = 'checkbox';

    public function init()
    {
        parent::init();
        $this->setAttrib('type', 'checkbox');
    }

    /**
     * @param $request
     * @return $this
     */
    public function getDataFromRequest($request)
    {
        $this->setChecked($request != null);
        return parent::getDataFromRequest($request);
    }

    /**
     * @param boolean $checked
     * @return $this
     */
    public function setChecked($checked)
    {
        if ($checked === true) {
            $this->setAttrib('checked', 'checked');
        } else {
            $this->delAttrib('checked');
        }
        return $this;
    }

    /**
     * @param $data
     * @return $this
     */
    public function getDataFromModel($value)
    {
        $inputValue = $this->getValue();
        if ($inputValue == null && $value) {
            $this->setChecked(true);
        }
        return parent::getDataFromModel($value);
    }

    /**
     * @return bool
     */
    public function isChecked()
    {
        return $this->getAttrib('checked') == 'checked';
    }
}
