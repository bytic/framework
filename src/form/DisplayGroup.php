<?php

class Nip_Form_DisplayGroup extends Nip_Collection
{
    /**
     * Group attributes
     * @var array
     */
    protected $_attribs = array();
    
    /**
     * @var Nip_Form
     */
    protected $_form;


    /**
     * @param  Nip_Form $form
     * @return Nip_Form_DisplayGroup
     */
    public function setForm(Nip_Form $form)
    {
        $this->_form = $form;
        return $this;
    }

    /**
     * @return Nip_Form|null
     */
    public function getForm()
    {
        return $this->_form;
    }

    public function addElement(Nip_Form_Element_Abstract $element)
    {
        $this[$element->getUniqueId()] = $element;
        return $this;
    }

    public function setLegend($legend)
    {
        return $this->setAttrib('legend', (string) $legend);
    }

    public function getLegend()
    {
        return $this->getAttrib('legend');
    }

    public function setAttrib($key, $value)
    {
        $key = (string) $key;
        $this->_attribs[$key] = $value;
        return $this;
    }

    public function addAttribs(array $attribs)
    {
        foreach ($attribs as $key => $value) {
            $this->setAttrib($key, $value);
        }
        return $this;
    }

    public function setAttribs(array $attribs)
    {
        $this->clearAttribs();
        return $this->addAttribs($attribs);
    }

    public function getAttrib($key)
    {
        $key = (string) $key;
        if (!isset($this->_attribs[$key])) {
            return null;
        }

        return $this->_attribs[$key];
    }

    public function getAttribs()
    {
        return $this->_attribs;
    }

    public function removeAttrib($key)
    {
        if (array_key_exists($key, $this->_attribs)) {
            unset($this->_attribs[$key]);
            return true;
        }

        return false;
    }

    public function clearAttribs()
    {
        $this->_attribs = array();
        return $this;
    }

    public function render() {
        return $this->getRenderer()->render();
    }

    /**
     * @return Nip_Form_Renderer
     */
    public function getRenderer() {
        if (!$this->_renderer) {
            $this->_renderer = $this->getNewRenderer();
        }
        return $this->_renderer;
    }

    /**
     * @return Nip_Form_Renderer_DisplayGroup
     */
    public function getNewRenderer($type = 'basic') {
        $name = 'Nip_Form_Renderer_DisplayGroup';
        $renderer = new $name();
        $renderer->setGroup($this);
        return $renderer;
    }


}