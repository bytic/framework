<?php

use Nip\Form\AbstractForm;

abstract class Nip_Form_Button_Abstract
{
    protected $_form;
    protected $_attribs;
    protected $_uniqueID;

    protected $_type = 'abstract';

    public function __construct($form)
    {
        $this->setForm($form);
        $this->init();
    }

    public function init()
    {
        $this->addClass('btn', 'btn-primary');
    }

    public function addClass()
    {
        $classes = func_get_args();
        if (is_array($classes)) {
            $oldClasses = explode(' ', $this->getAttrib('class'));
            $classes = array_merge($classes, $oldClasses);
            $this->setAttrib('class', implode(' ', $classes));
        }

        return $this;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public function getAttrib($key)
    {
        $key = (string) $key;
        if (!isset($this->_attribs[$key])) {
            return null;
        }

        return $this->_attribs[$key];
    }

    /**
     * @return Nip_Form_Button_Abstract
     */
    public function setAttrib($key, $value)
    {
        $key = (string) $key;
        $this->_attribs[$key] = $value;

        return $this;
    }

    public function setId($id)
    {
        $this->setAttrib('id', $id);

        return $this;
    }

    public function getId()
    {
        return $this->getAttrib('id');
    }

    public function setName($name)
    {
        $this->setAttrib('name', $name);

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getAttrib('name');
    }

    /**
     * @param string $label
     * @return $this
     */
    public function setLabel($label)
    {
        $this->setAttrib('label', $label);

        return $this;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->getAttrib('label');
    }

    /**
     * @param $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->setAttrib('value', $value);

        return $this;
    }

    /**
     * @param string $requester
     * @return string
     */
    public function getValue($requester = 'abstract')
    {
        return $this->getAttrib('value');
    }

    /**
     * @param $key
     * @return bool
     */
    public function delAttrib($key)
    {
        $key = (string) $key;
        unset($this->_attribs[$key]);

        return true;
    }

    public function getAttribs()
    {
        return $this->_attribs;
    }

    /**
     * @param  array $attribs
     * @return Nip_Form_Button_Abstract
     */
    public function setAttribs(array $attribs)
    {
        $this->clearAttribs();

        return $this->addAttribs($attribs);
    }

    /**
     * @return Nip_Form_Button_Abstract
     */
    public function clearAttribs()
    {
        $this->_attribs = [];

        return $this;
    }

    /**
     * @param  array $attribs
     * @return Nip_Form_Button_Abstract
     */
    public function addAttribs(array $attribs)
    {
        foreach ($attribs as $key => $value) {
            $this->setAttrib($key, $value);
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function removeAttrib($key)
    {
        if (isset($this->_attribs[$key])) {
            unset($this->_attribs[$key]);

            return true;
        }

        return false;
    }

    public function render()
    {
        return $this->getRenderer()->render($this);
    }

    public function getRenderer()
    {
        return $this->getForm()->getRenderer()->getButtonRenderer($this);
    }

    /**
     * @return AbstractForm
     */
    public function getForm()
    {
        return $this->_form;
    }

    /**
     * @param AbstractForm $form
     * @return $this
     */
    public function setForm(AbstractForm $form)
    {
        $this->_form = $form;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }
}
