<?php

use Nip\Collection;
use Nip\Form\AbstractForm;

/**
 * Class Nip_Form_DisplayGroup
 */
class Nip_Form_DisplayGroup extends Collection
{
    /**
     * Group attributes
     * @var array
     */
    protected $_attribs = [];

    /**
     * @var Nip_Form
     */
    protected $_form;

    protected $renderer;

    /**
     * @return Nip_Form|null
     */
    public function getForm()
    {
        return $this->_form;
    }

    /**
     * @param  AbstractForm $form
     * @return Nip_Form_DisplayGroup
     */
    public function setForm(AbstractForm $form)
    {
        $this->_form = $form;

        return $this;
    }

    /**
     * @param Nip_Form_Element_Abstract $element
     * @return $this
     */
    public function addElement(Nip_Form_Element_Abstract $element)
    {
        $this[$element->getUniqueId()] = $element;

        return $this;
    }

    /**
     * @param string $legend
     * @return Nip_Form_DisplayGroup
     */
    public function setLegend($legend)
    {
        return $this->setAttrib('legend', (string) $legend);
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function setAttrib($key, $value)
    {
        $key = (string) $key;
        $this->_attribs[$key] = $value;

        return $this;
    }

    /**
     * @return mixed|null
     */
    public function getLegend()
    {
        return $this->getAttrib('legend');
    }

    /**
     * @param string $key
     * @return mixed|null
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
     * @return array
     */
    public function getAttribs()
    {
        return $this->_attribs;
    }

    /**
     * @param array $attribs
     * @return Nip_Form_DisplayGroup
     */
    public function setAttribs(array $attribs)
    {
        $this->clearAttribs();

        return $this->addAttribs($attribs);
    }

    /**
     * @return $this
     */
    public function clearAttribs()
    {
        $this->_attribs = [];

        return $this;
    }

    /**
     * @param array $attribs
     * @return $this
     */
    public function addAttribs(array $attribs)
    {
        foreach ($attribs as $key => $value) {
            $this->setAttrib($key, $value);
        }

        return $this;
    }

    /**
     * @param $key
     * @return bool
     */
    public function removeAttrib($key)
    {
        if (array_key_exists($key, $this->_attribs)) {
            unset($this->_attribs[$key]);

            return true;
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function render()
    {
        return $this->getRenderer()->render();
    }

    /**
     * @return Nip_Form_Renderer_DisplayGroup
     */
    public function getRenderer()
    {
        if (!$this->renderer) {
            $this->renderer = $this->getNewRenderer();
        }

        return $this->renderer;
    }

    /**
     * @param string $type
     * @return Nip_Form_Renderer_DisplayGroup
     */
    public function getNewRenderer($type = 'basic')
    {
        $name = 'Nip_Form_Renderer_DisplayGroup';
        $renderer = new $name();
        $renderer->setGroup($this);

        return $renderer;
    }
}
