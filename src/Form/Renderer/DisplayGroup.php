<?php

class Nip_Form_Renderer_DisplayGroup
{
   
    /**
     * @var Nip_Form_DisplayGroup
     */
    protected $_group;


    /**
     * @return Nip_Form_Renderer_DisplayGroup
     */
    public function setGroup(Nip_Form_DisplayGroup $group)
    {
        $this->_group = $group;
        return $this;
    }

    /**
     * @return Nip_Form_Renderer_DisplayGroup|null
     */
    public function getGroup()
    {
        return $this->_group;
    }
    
    public function render()
    {
        $return = '<fieldset' . $this->renderAttributes() . '>';
        $return .= '<legend>' . $this->getGroup()->getLegend() . '</legend>';

        $renderer = clone $this->getGroup()->getForm()->getRenderer();
        $renderer->setElements($this->getGroup()->toArray());
        $return .= $renderer->renderElements();
        $return .= '</fieldset>';
        return $return;
    }
    
    public function renderAttributes($overrides = array())
    {
        $attribs = $this->getGroup()->getAttribs();
        $elementAttribs = $this->getElementAttribs();
        $return = '';
        foreach ($attribs as $name => $value) {
            if (in_array($name, $elementAttribs)) {
                if (in_array($name, array_keys($overrides))) {
                    $value = $overrides[$name];
                }
                $return .= ' ' . $name . '="' . $value . '"';
            }
        }
        return $return;
    }

    public function getElementAttribs()
    {
        return array('id', 'style', 'class');
    }
}
