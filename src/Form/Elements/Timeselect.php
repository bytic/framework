<?php

class Nip_Form_Element_Timeselect extends Nip_Form_Element_MultiElement
{
    protected $_type = 'timeselect';

    public function init()
    {
        parent::init();

        $this->initSelects();
    }

    public function initSelects()
    {
        if (!isset($this->elements['hours'])) {
            $hoursElement = $this->getForm()->getNewSelectElement();

            $hoursElement->addOption('-', 'HH');
            for ($i = 0; $i <= 24; $i++) {
                $hoursElement->addOption($i, $i.'h');
            }
            $hoursElement->setValue('-');

            $this->elements['hours'] = $hoursElement;
        }


        if (!isset($this->elements['minutes'])) {
            $minutesElement = $this->getForm()->getNewSelectElement();

            $minutesElement->addOption('-', 'MM');
            for ($i = 0; $i <= 59; $i++) {
                $minutesElement->addOption($i, $i.'m');
            }
            $minutesElement->setValue('-');

            $this->elements['minutes'] = $minutesElement;
        }

        if (!isset($this->elements['seconds'])) {
            $secondsElement = $this->getForm()->getNewSelectElement();

            $secondsElement->addOption('-', 'SS');
            for ($i = 0; $i <= 59; $i++) {
                $secondsElement->addOption($i, $i.'s');
            }
            $secondsElement->setValue('-');

            $this->elements['seconds'] = $secondsElement;
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
        $this->elements['hours']->setName($inputName.'[hours]');
        $this->elements['minutes']->setName($inputName.'[minutes]');
        $this->elements['seconds']->setName($inputName.'[seconds]');
    }

    /**
     * @inheritdoc
     */
    public function getData($data, $source = 'abstract')
    {
        if ($source == 'model') {
            $dateUnix = strtotime($data);
            if ($dateUnix && $dateUnix !== false && $dateUnix > -62169989992) {
                $this->elements['hours']->setValue(date('H', $dateUnix));
                $this->elements['minutes']->setValue(date('i', $dateUnix));
                $this->elements['seconds']->setValue(date('s', $dateUnix));
            }

            return $this;
        }

        return parent::getData($data, $source);
    }

    /** @noinspection PhpMissingParentCallCommonInspection
     * @inheritdoc
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
                $expectedValue = str_pad(intval($this->elements['hours']->getValue()), 2, "0", STR_PAD_LEFT);
                $expectedValue .= ':'.str_pad(intval($this->elements['minutes']->getValue()), 2, "0", STR_PAD_LEFT);
                $expectedValue .= ':'.str_pad(intval($this->elements['seconds']->getValue()), 2, "0", STR_PAD_LEFT);
                if ($expectedValue != $value) {
                    $message = $this->getForm()->getMessageTemplate('bad-'.$this->getName());
                    $message = $message ? $message : 'I couldn\'t parse the '.strtolower($this->getLabel()).' you entered';
                    $this->addError($message);
                }
            }
        }
    }

    /** @noinspection PhpMissingDocCommentInspection
     * @inheritdoc
     */
    public function getValue($requester = 'abstract')
    {
        $unixTime = $this->getUnix();
        $format = $requester == 'model' ? 'H:i:s' : 'H:i:s';

        $value = ($unixTime) ? date($format, $unixTime) : null;

        return $value;
    }


    /** @noinspection PhpMissingDocCommentInspection
     * @inheritdoc
     */
    public function getUnix($format = false)
    {
        $hour = intval($this->elements['hours']->getValue());
        $minutes = intval($this->elements['minutes']->getValue());
        $seconds = intval($this->elements['seconds']->getValue());
        if ($hour + $minutes + $seconds > 0) {
            return mktime($hour, $minutes, $seconds);
        }

        return false;
    }
}
