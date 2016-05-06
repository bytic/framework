<?php
class Nip_Form_Element_Checkbox extends Nip_Form_Element_Input_Abstract {

    protected $_type = 'checkbox';

    public function init() {
        parent::init();
        $this->setAttrib('type', 'checkbox');
    }

	public function getDataFromRequest($request) {
		$this->setChecked($request != null);			
        return parent::getDataFromRequest($request);
    }
	
    public function getDataFromModel($value) {
		$inputValue = $this->getValue();
		if ($inputValue == null && $value) {
			$this->setChecked(true);
		}
		return parent::getDataFromModel($data);
    }
	
    public function isChecked() {
        return $this->getAttrib('checked') == 'checked';
    }

    public function setChecked($checked) {
        if ($checked === true) {
            $this->setAttrib('checked', 'checked');
        } else {
            $this->delAttrib('checked');
        }
        return $this;
    }
	
}