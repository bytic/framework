<?php

use Nip\Form\Renderer\AbstractRenderer;

abstract class Nip_Form_Renderer_Elements_Abstract
{
    protected $_renderer;
    protected $_element;

    /**
     * @return AbstractRenderer
     */
    public function getRenderer()
    {
        return $this->_renderer;
    }

    /**
     * @param AbstractRenderer $renderer
     * @return $this
     */
    public function setRenderer(AbstractRenderer $renderer)
    {
        $this->_renderer = $renderer;

        return $this;
    }

    /**
     * @return string
     */
    public function render()
    {
        $return = '';
        $return .= $this->renderElement();

        $renderErrors = $this->getElement()->getForm()->getOption('render_input_errors');
        if ($renderErrors !== false) {
            $return .= $this->renderErrors();
        }
        $this->getElement()->setRendered(true);

        return $return;
    }

    /**
     * @return mixed
     */
    public function renderElement()
    {
        $return = $this->renderDecorators($this->generateElement(), 'element');
        $this->getElement()->setRendered(true);

        return $return;
    }

    /**
     * @param $return
     * @param bool $position
     * @return mixed
     */
    public function renderDecorators($return, $position = false)
    {
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

    /**
     * @return Nip_Form_Element_Abstract
     */
    public function getElement()
    {
        return $this->_element;
    }

    public function setElement(Nip_Form_Element_Abstract $element)
    {
        $this->_element = $element;

        return $this;
    }

    public function generateElement()
    {
        return;
    }

    public function renderErrors()
    {
        $return = '';
        if ($this->getElement()->isError() && $this->getElement()->getForm()->getOption('renderElementErrors') !== false) {
            $errors = $this->getElement()->getErrors();
            $errors_string = implode('<br />', $errors);
            $return .= '<span class="help-inline">'.$errors_string.'</span>';
        }

        return $return;
    }

    /**
     * @param array $overrides
     * @return string
     */
    public function renderAttributes($overrides = [])
    {
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
                $return .= ' '.$name.'="'.$value.'"';
            }
        }

        return $return;
    }

    /**
     * @return array
     */
    public function getElementAttribs()
    {
        return ['id', 'name', 'style', 'class', 'title', 'readonly', 'disabled'];
    }
}
