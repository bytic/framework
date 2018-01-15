<?php

abstract class Nip_Form_Renderer_Button_Abstract
{
    protected $_renderer;
    protected $_button;

    public function setRenderer(Nip_Form_Renderer_Abstract $renderer)
    {
        $this->_renderer = $renderer;

        return $this;
    }

    /**
     * @return Nip_Form_Renderer_Abstract
     */
    public function getRenderer()
    {
        return $this->_renderer;
    }

    public function setItem(Nip_Form_Button_Abstract $item)
    {
        $this->_item = $item;

        return $this;
    }

    /**
     * @return Nip_Form_Button_Abstract
     */
    public function getItem()
    {
        return $this->_item;
    }

    public function render()
    {
        $return = '';
        $return .= $this->renderItem();

        return $return;
    }

    public function renderItem()
    {
        $return = $this->generateItem();

        return $return;
    }

    public function generateItem()
    {
    }

    public function renderAttributes($overrides = [])
    {
        $attribs = $this->getItem()->getAttribs();
        if (!isset($attribs['title'])) {
            $attribs['title'] = $this->getItem()->getLabel();
        }
        $itemAttribs = $this->getItemAttribs();
        $return = '';
        foreach ($attribs as $name => $value) {
            if (in_array($name, $itemAttribs)) {
                if (in_array($name, array_keys($overrides))) {
                    $value = $overrides[$name];
                }

                $return .= ' '.$name.'="'.$value.'"';
            }
        }

        return $return;
    }

    public function getItemAttribs()
    {
        return ['id', 'name', 'style', 'class', 'title', 'read_only', 'disabled'];
    }
}
