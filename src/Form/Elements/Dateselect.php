<?php

use function Nip\locale;

/**
 * Class Nip_Form_Element_Dateselect
 */
class Nip_Form_Element_Dateselect extends Nip_Form_Element_MultiElement
{
    protected $_type = 'dateselect';
    protected $_locale = 'ct_EN';
    protected $format = 'M d Y';

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

        if (!$this->hasElement('day')) {
            $dayElement = $this->getForm()->getNewElement('select');

            for ($i = 1; $i <= 31; $i++) {
                $dayElement->addOption($i, $i);
            }
            $dayElement->setValue(date('d'));
            $this->elements['day'] = $dayElement;
        }


        if (!$this->hasElement('month')) {
            $monthElement = $this->getForm()->getNewElement('select');
            for ($i = 1; $i <= 12; $i++) {
                $monthElement->addOption($i, date('M', mktime(0, 0, 0, $i, 1, 2014)));
            }
            $monthElement->setValue(date('m'));
            $this->elements['month'] = $monthElement;
        }

        if (!$this->hasElement('year')) {
            $yearElement = $this->getForm()->getNewElement('select');
            $currentYear = date('Y');
            $startYear = $currentYear - 100;
            $endYear = $currentYear + 5;
            for ($i = $startYear; $i <= $endYear; $i++) {
                $yearElement->addOption($i, $i);
            }
            $yearElement->setValue(date('Y'));
            $this->elements['year'] = $yearElement;
        }
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        $return = parent::setName($name);
        $this->updateNameSelects();
        return $return;
    }

    public function updateNameSelects()
    {
        $inputName = $this->getName();
        $this->getElement('day')->setName($inputName . '[day]');
        $this->getElement('month')->setName($inputName . '[month]');
        $this->getElement('year')->setName($inputName . '[year]');
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
        return $this->format;
    }

    /**
     * @param $format
     */
    public function setFormat($format)
    {
        $this->format = $format;
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
                    $this->getElement('day')->setValue(date('d', $dateUnix));
                    $this->getElement('month')->setValue(date('m', $dateUnix));
                    $this->getElement('year')->setValue(date('Y', $dateUnix));
                }
            }
            return $this;
        }
        return parent::getData($data, $source);
    }

    /** @noinspection PhpMissingParentCallCommonInspection
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

    /** @noinspection PhpMissingParentCallCommonInspection
     * @param string $requester
     * @return string|null
     */
    public function getValue($requester = 'abstract')
    {
        $unixTime = $this->getUnix();
        $format = $requester == 'model' ? 'Y-m-d' : $this->format;
        if ($unixTime) {
            return date($format, $unixTime);
        }

        return;
    }

    /**
     * @param bool $format
     * @return false|int
     */
    public function getUnix($format = false)
    {
        $day = $this->elements['day']->getValue();
        $month = $this->elements['month']->getValue();
        $year = $this->elements['year']->getValue();

        return mktime(0, 0, 0, $month, $day, $year);
    }
}
