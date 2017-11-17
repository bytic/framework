<?php

namespace Nip\Form\Traits;

use Nip\Form\AbstractForm;

/**
 * Class MagicMethodElementsFormTrait
 * @package Nip\Form
 *
 * @method addInput($name, $label = false, $isRequired = false)
 * @method addHidden($name, $label = false, $isRequired = false)
 * @method addSelect($name, $label = false, $isRequired = false)
 * @method addDateinput($name, $label = false, $isRequired = false)
 * @method addRadioGroup($name, $label = false, $isRequired = false)
 * @method addBsRadioGroup($name, $label = false, $isRequired = false)
 * @method addTextarea($name, $label = false, $isRequired = false)
 * @method addTextSimpleEditor($name, $label = false, $isRequired = false)
 * @method addFile($name, $label = false, $isRequired = false)
 */
trait MagicMethodElementsFormTrait
{
    protected $elementsTypes = [
        'input',
        'hidden',
        'password',
        'hash',
        'file',
        'multiElement',
        'dateinput',
        'dateselect',
        'timeselect',
        'textarea',
        'texteditor',
        'textSimpleEditor',
        'textMiniEditor',
        'select',
        'radio',
        'radioGroup',
        'checkbox',
        'checkboxGroup',
        'html',
    ];

    /**
     * @param $name
     * @param $arguments
     * @return AbstractForm|self
     */
    public function __call($name, $arguments)
    {
        $addElements = $this->detectMagicMethodAddElements($name, $arguments);
        if ($addElements !== false) {
            return $addElements;
        }

        trigger_error('Call to undefined method: [' . $name . ']', E_USER_ERROR);

        return $this;
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    protected function detectMagicMethodAddElements($name, $arguments)
    {
        if ($this->isMagicMethodAddElements($name)) {
            $type = str_replace('add', '', $name);
            $type[0] = strtolower($type[0]);
            if ($this->isElementsType($type)) {
                return $this->magicMethodAddElement($type, $arguments);
            } else {
                trigger_error('Undefined element type for add operation: [' . $type . ']', E_USER_ERROR);
            }
        }

        return false;
    }

    /**
     * @param $name
     * @return bool
     */
    protected function isMagicMethodAddElements($name)
    {
        return strpos($name, 'add') === 0;
    }

    /**
     * @param string[] $type
     * @return boolean
     */
    public function isElementsType($type)
    {
        return in_array($type, $this->getElementsTypes());
    }

    /**
     * @return array
     */
    public function getElementsTypes()
    {
        return $this->elementsTypes;
    }

    /**
     * @param array $elementsTypes
     */
    public function setElementsTypes($elementsTypes)
    {
        $this->elementsTypes = $elementsTypes;
    }

    /**
     * @param string[] $type
     * @param $arguments
     * @return mixed
     */
    protected function magicMethodAddElement($type, $arguments)
    {
        $name = $this->getElementNameFromMagicMethodArguments($arguments);
        $label = $this->getElementLabelFromMagicMethodArguments($arguments);
        $isRequired = $this->getElementIsRequiredFromMagicMethodArguments($arguments);

        return $this->add($name, $label, $type, $isRequired);
    }

    /**
     * @param $arguments
     * @return mixed
     */
    protected function getElementNameFromMagicMethodArguments($arguments)
    {
        return $arguments[0];
    }

    /**
     * @param $arguments
     * @return string|false
     */
    protected function getElementLabelFromMagicMethodArguments($arguments)
    {
        return isset($arguments[1]) ? $arguments[1] : false;
    }

    /** @noinspection PhpMethodNamingConventionInspection
     * @param $arguments
     * @return string|false
     */
    protected function getElementIsRequiredFromMagicMethodArguments($arguments)
    {
        return isset($arguments[2]) ? $arguments[2] : false;
    }
}
