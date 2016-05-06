<?php
abstract class Nip_Form_Renderer_Elements_Abstract {

    protected $_renderer;
    protected $_element;
    
    public function setRenderer(Nip_Form_Renderer_Abstract $renderer) {
        $this->_renderer = $renderer;
        return $this;
    }

    /**
     * @return Nip_Form_Renderer_Abstract
    */
    public function getRenderer() {
        return $this->_renderer;
    }

    public function setElement(Nip_Form_Element_Abstract $element) {
        $this->_element = $element;
        return $this;
    }

    /**
     * @return Nip_Form_Element_Abstract
    */
    public function getElement() {
        return $this->_element;
    }


    public function render() {
        $return = '';
        $return .= $this->renderElement();
        
        $renderErrors = $this->getElement()->getForm()->getOption('render_input_errors');
        if ($renderErrors !== false) {
            $return .= $this->renderErrors();
        }
        $this->getElement()->setRendered(true);
        return $return;
    }

    public function renderElement() {
        $return =  $this->renderDecorators($this->generateElement(), 'element');
        $this->getElement()->setRendered(true);
        return $return;
    }
    
    public function generateElement() {
        return;
    }
    
    public function renderDecorators($return, $position = false) {
        if ($position) {
            $decorators = $this->getElement()->getDecoratorsByPosition($position);
            if (is_array($decorators)) {
                foreach ($decorators as $decorator) {
                    $return = $decorator->render($return);
                }
            }
        }
        return $return;
    }

    public function renderErrors() {
        $return = '';
        if ($this->getElement()->isError() && $this->getElement()->getForm()->getOption('renderElementErrors') !== false) {
            $errors = $this->getElement()->getErrors();
            $errors_string = implode('<br />', $errors);            
            $return .= '<span class="help-inline">' . $errors_string .'</span>';
        }
        return $return;
    }

    public function renderAttributes($overrides = array()) {
        $attribs = $this->getElement()->getAttribs();
        if (!isset($attribs['title'])) {
            $attribs['title'] = $this->getElement()->getLabel();
        }
        $elementAttribs = $this->getElementAttribs();
        $return = '';
        foreach ($attribs as $name => $value) {
            if (in_array($name, $elementAttribs)) {
                if (in_array($name, array_keys($overrides))) {
                    $value = $overrides[$name];
                }
                if ($name == "name" && $this->getElement()->isGroup()) {
                    $value = $value."[]";
                }
                $return .= ' ' . $name . '="' . $value . '"';
            }
        }
        return $return;
    }

    public function getElementAttribs() {
        return array('id', 'name', 'style', 'class', 'title', 'readonly', 'disabled');
    }

}