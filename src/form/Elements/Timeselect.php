<?php

class Nip_Form_Element_Timeselect extends Nip_Form_Element_MultiElement {

    protected $_type = 'timeselect';

    public function init() {
        parent::init();

        $this->initSelects();
    }

    public function initSelects() {
        $inputName = $this->getName();

        if (!$this->_elements['hours']) {
            $hoursElement = $this->getForm()->getNewElement('select');

            $hoursElement->addOption('-', 'HH');
            for ($i=0; $i<=24 ; $i++) {
                $hoursElement->addOption($i, $i.'h');
            }
            $hoursElement->setValue('-');

            $this->_elements['hours'] = $hoursElement;
        }


        if (!$this->_elements['minutes']) {
            $minutesElement = $this->getForm()->getNewElement('select');

            $minutesElement->addOption('-', 'MM');
            for ($i=0; $i<=59 ; $i++) {
                $minutesElement->addOption($i, $i.'m');
            }
            $minutesElement->setValue('-');

            $this->_elements['minutes'] = $minutesElement;
        }

        if (!$this->_elements['seconds']) {
            $secondsElement = $this->getForm()->getNewElement('select');

            $secondsElement->addOption('-', 'SS');
            for ($i=0; $i<=59 ; $i++) {
                $secondsElement->addOption($i, $i.'s');
            }
            $secondsElement->setValue('-');

            $this->_elements['seconds'] = $secondsElement;
        }
    }

    public function setName($name)
    {
        $return = parent::setName($name);
        $this->updateNameSelects();
        return $return;
    }

    public function updateNameSelects()
    {
        $inputName = $this->getName();
        $this->_elements['hours']->setName($inputName . '[hours]');
        $this->_elements['minutes']->setName($inputName . '[minutes]');
        $this->_elements['seconds']->setName($inputName . '[seconds]');
    }

    public function getData($data, $source = 'abstract') {
        if ($source == 'model') {
            $dateUnix = strtotime($data);
            if ($dateUnix && $dateUnix !== false && $dateUnix > -62169989992) {
                $this->_elements['hours']->setValue(date('H', $dateUnix));
                $this->_elements['minutes']->setValue(date('i', $dateUnix));
                $this->_elements['seconds']->setValue(date('s', $dateUnix));
            }
            return $this;
        }
        return parent::getData($data, $source);
    }

    public function getDataFromRequest($request) {
        if (is_array($request)) {
            $elements = $this->getElements();
            foreach ($elements as $key=>$element) {
                $value = $request[$key];
                if ($value > 0) {
                    $element->setValue($value);
                }
            }
        }
        return $this;
    }

    public function  validate() {
        parent::validate();
        if (!$this->isError()) {
            $value = $this->getValue();
            if ($value) {
                $expectedValue = str_pad(intval($this->_elements['hours']->getValue()), 2, "0", STR_PAD_LEFT);
                $expectedValue .= ':'. str_pad(intval($this->_elements['minutes']->getValue()), 2, "0", STR_PAD_LEFT);
                $expectedValue .= ':'. str_pad(intval($this->_elements['seconds']->getValue()), 2, "0", STR_PAD_LEFT);
                if ($expectedValue != $value) {
                    $message = $this->getForm()->getMessageTemplate('bad-' . $this->getName());
                    $message = $message ? $message : 'I couldn\'t parse the ' . strtolower($this->getLabel()) . ' you entered';
                    $this->addError($message);
                }
            }
        }
    }

    public function getValue($requester = 'abstract')
    {
        $unixTime = $this->getUnix();
        $format = $requester == 'model' ? 'H:i:s' : 'H:i:s';
        if ($unixTime) {
            $value = date($format, $unixTime);
        }

        return $value;
    }

    public function getUnix($format = false)
    {
        $hour = intval($this->_elements['hours']->getValue());
        $minutes = intval($this->_elements['minutes']->getValue());
        $seconds = intval($this->_elements['seconds']->getValue());
        if ($hour+$minutes+$seconds > 0) {
            return mktime($hour,$minutes,$seconds);
        }
        return false;
    }

}