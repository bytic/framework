<?php
abstract class Nip_Form_Button_Abstract {

    protected $_form;
    protected $_attribs;
    protected $_uniqueID;
    
    protected $_type = 'abstract';

    public function  __construct($form) {
        $this->setForm($form);
        $this->init();
    }

    public function init() {
        $this->addClass('btn','btn-primary');
    }

    public function setId($id) {
        $this->setAttrib('id', $id);
        return $this;
    }

    public function getId() {
        return $this->getAttrib('id');
    }    

    public function setName($name) {
        $this->setAttrib('name', $name);
        return $this;
    }

    public function getName() {
        return $this->getAttrib('name');
    }

    public function setLabel($label) {
        $this->setAttrib('label', $label);
        return $this;
    }

    public function getLabel() {
        return $this->getAttrib('label');
    }

    public function setValue($value) {
        $this->setAttrib('value', $value);
        return $this;
    }

    public function getValue($requester = 'abstract') {
        return $this->getAttrib('value');
    }

    public function addClass() {
        $classes = func_get_args();
        if (is_array($classes)) {
            $oldClasses = explode(' ', $this->getAttrib('class'));
            $classes = array_merge($classes, $oldClasses);
            $this->setAttrib('class', implode(' ', $classes));
        }
        return $this;
    }

    public function setForm(Nip_Form_Abstract $form) {
        $this->_form = $form;
        return $this;
    }

    /**
     * @return Nip_Form_Abstract
     */
    public function getForm() {
        return $this->_form;
    }
    
    /**
     * @return Nip_Form_Element_Abstract
     */
    public function setAttrib($key, $value)  {
        $key = (string) $key;
        $this->_attribs[$key] = $value;
        return $this;
    }

    /**
     * @param  array $attribs
     * @return Nip_Form_Element_Abstract
     */
    public function addAttribs(array $attribs) {
        foreach ($attribs as $key => $value) {
            $this->setAttrib($key, $value);
        }
        return $this;
    }

    /**
     * @param  array $attribs
     * @return Nip_Form_Element_Abstract
     */
    public function setAttribs(array $attribs) {
        $this->clearAttribs();
        return $this->addAttribs($attribs);
    }

    public function getAttrib($key) {
        $key = (string) $key;
        if (!isset($this->_attribs[$key])) {
            return null;
        }

        return $this->_attribs[$key];
    }

    public function delAttrib($key) {
        $key = (string) $key;
        unset($this->_attribs[$key]);

        return true;
    }

    public function getAttribs() {
        return $this->_attribs;
    }

    /**
     * @return bool
     */
    public function removeAttrib($key) {
        if (isset($this->_attribs[$key])) {
            unset($this->_attribs[$key]);
            return true;
        }

        return false;
    }

    /**
     * @return Nip_Form_Element_Abstract
     */
    public function clearAttribs() {
        $this->_attribs = array();
        return $this;
    }

    public function getRenderer() {
        return $this->getForm()->getRenderer()->getButtonRenderer($this);
    }

    public function render() {
        return $this->getRenderer()->render($this);
    }

    public function getType() {
        return $this->_type;
    }

}