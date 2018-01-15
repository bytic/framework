<?php

class Nip_Form_Element_File extends Nip_Form_Element_Input_Abstract
{
    protected $_value;

    public function init()
    {
        parent::init();
        $this->setAttrib('type', 'file');
        $this->getForm()->setAttrib('enctype', 'multipart/form-data');
    }

    public function getValue($requester = 'abstract')
    {
        if (!$this->_value) {
            $name = $this->getName();
            $name = str_replace(']', '', $name);
            $parts = explode('[', $name);

            if (count($parts) > 1) {
                if ($_FILES[$parts[0]]) {
                    $fileData = [];
                    foreach ($_FILES[$parts[0]] as $key=>$data) {
                        $fileData[$key] = $data[$parts[1]];
                    }
                    $this->_value = $fileData;
                } else {
                    $this->_value = null;
                }
            } else {
                $this->_value = $_FILES[$name];
            }
        }

        return $this->_value;
    }
}
