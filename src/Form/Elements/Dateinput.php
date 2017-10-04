<?php

use function Nip\locale;

class Nip_Form_Element_Dateinput extends Nip_Form_Element_Input
{
    protected $_type = 'dateinput';
    protected $_locale = 'ct_EN';
    protected $_format = 'M d Y';
    protected $_hasTime = false;

    public function init()
    {
        parent::init();
        $this->setLocale(locale()->getCurrent());
        $this->setFormat(locale()->getOption(['time', 'dateFormat']));
    }

    public function getLocale()
    {
        return $this->_locale;
    }

    public function setLocale($format)
    {
        $this->_locale = $format;
    }

    public function getFormat()
    {
        return $this->_format;
    }

    public function setFormat($format)
    {
        $this->_format = $format;
    }

    public function setTime($time)
    {
        $this->_hasTime = (bool) $time;
    }

    public function hasTime()
    {
        return $this->_hasTime;
    }

    public function getData($data, $source = 'abstract')
    {
        if ($source == 'model') {
            if ($data && $data != '0000-00-00' && $data != '0000-00-00 00:00:00') {
                $dateUnix = strtotime($data);
                if ($dateUnix && $dateUnix !== false && $dateUnix > -62169989992) {
                    $this->setValue(date($this->_format, $dateUnix));
                    return $this;
                }
            }
            $this->setValue('');
            return $this;
        }
        return parent::getData($data, $source);
    }

    public function validate()
    {
        parent::validate();
        if (!$this->isError() && $this->getValue()) {
            $this->validateFormat();
        }
    }

    public function getValue($requester = 'abstract')
    {
        $value = parent::getValue($requester);
        if ($requester == 'model') {
            if ($value) {
                $unixTime = $this->getUnix();
                $value = date('Y-m-d', $unixTime);
            }
        }
        return $value;
    }

    public function getUnix($format = false)
    {
        $format = $format ? $format : $this->_format;
        $value = $this->getValue();
        if ($value) {
            $date = date_create_from_format($format, $this->getValue());
        }

        return $date ? $date->getTimestamp() : false;
    }

    public function validateFormat($format = false)
    {
        $format = $format ? $format : $this->_format;
        $value = $this->getValue();

        if ($value) {
            $unixTime = $this->getUnix($format);
            if ($unixTime) {
                $this->setValue(date($format, $unixTime));
                return true;
            }
            $message = $this->getForm()->getMessageTemplate('bad-' . $this->getName());
            $message = $message ? $message : 'I couldn\'t parse the ' . strtolower($this->getLabel()) . ' you entered';
            $this->addError($message);
        }
    }
}
