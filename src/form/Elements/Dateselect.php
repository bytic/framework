<?php

use function Nip\locale;

class Nip_Form_Element_Dateselect extends Nip_Form_Element_MultiElement
{

    protected $_type = 'dateselect';
    protected $_locale = 'ct_EN';
    protected $_format = 'M d Y';

    public function init()
    {
        parent::init();
        $this->setLocale(locale()->getCurrent());
        $this->setFormat(locale()->getOption(['time', 'dateFormat']));

        $this->initSelects();
    }

    public function initSelects()
    {
        $inputName = $this->getName();

        if (!$this->_elements['day']) {
            $dayElement = $this->getForm()->getNewElement('select');

            for ($i = 1; $i <= 31; $i++) {
                $dayElement->addOption($i, $i);
            }
            $dayElement->setValue(date('d'));
            $this->_elements['day'] = $dayElement;
        }


        if (!$this->_elements['month']) {
            $monthElement = $this->getForm()->getNewElement('select');
            for ($i = 1; $i <= 12; $i++) {
                $monthElement->addOption($i, date('M', mktime(0, 0, 0, $i, 1, 2014)));
            }
            $monthElement->setValue(date('m'));
            $this->_elements['month'] = $monthElement;
        }

        if (!$this->_elements['year']) {
            $yearElement = $this->getForm()->getNewElement('select');
            $curentYear = date('Y');
            $startYear = $curentYear - 100;
            $endYear = $curentYear + 5;
            for ($i = $startYear; $i <= $endYear; $i++) {
                $yearElement->addOption($i, $i);
            }
            $yearElement->setValue(date('Y'));
            $this->_elements['year'] = $yearElement;
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
        $this->_elements['day']->setName($inputName . '[day]');
        $this->_elements['month']->setName($inputName . '[month]');
        $this->_elements['year']->setName($inputName . '[year]');
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->_locale;
    }

    /**
     * @param $format
     */
    public function setLocale($format)
    {
        $this->_locale = $format;
    }

    /**
     * @return string
     */
    public function getFormat()
    {
        return $this->_format;
    }

    /**
     * @param $format
     */
    public function setFormat($format)
    {
        $this->_format = $format;
    }

    /**
     * @param $data
     * @param string $source
     * @return Nip_Form_Element_Abstract
     */
    public function getData($data, $source = 'abstract')
    {
        if ($source == 'model') {
            if ($data && $data != '0000-00-00' && $data != '0000-00-00 00:00:00') {
                $dateUnix = strtotime($data);
                if ($dateUnix && $dateUnix !== false && $dateUnix > -62169989992) {
                    $this->_elements['day']->setValue(date('d', $dateUnix));
                    $this->_elements['month']->setValue(date('m', $dateUnix));
                    $this->_elements['year']->setValue(date('Y', $dateUnix));
                }
            }
            return $this;
        }
        return parent::getData($data, $source);
    }

    /**
     * @param $request
     * @return $this
     */
    public function getDataFromRequest($request)
    {
        if (is_array($request)) {
            $elements = $this->getElements();
            foreach ($elements as $key => $element) {
                $value = $request[$key];
                if ($value > 0) {
                    $element->setValue($value);
                }
            }
        }
        return $this;
    }

    public function validate()
    {
        parent::validate();
        if (!$this->isError()) {
            $value = $this->getValue();
            if ($value) {

            }
        }
    }

    /**
     * @param string $requester
     * @return null
     */
    public function getValue($requester = 'abstract')
    {
        $unixTime = $this->getUnix();
        $format = $requester == 'model' ? 'Y-m-d' : $this->_format;
        if ($unixTime) {
            $value = date($format, $unixTime);
        }

        return $value;
    }

    /**
     * @param bool $format
     * @return false|int
     */
    public function getUnix($format = false)
    {
        $day = $this->_elements['day']->getValue();
        $month = $this->_elements['month']->getValue();
        $year = $this->_elements['year']->getValue();

        return mktime(0, 0, 0, $month, $day, $year);
    }
}
