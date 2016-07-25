<?php

use Nip\Helpers\View\Messages as MessagesHelper;
use Nip\Helpers\View\Errors as ErrorsHelper;

abstract class Nip_Form_Renderer_Abstract
{

    protected $_form;

    protected $_elements;
    protected $_elementsRenderer;

    protected $_buttonsRenderer;

    public function __construct()
    {
    }

    public function setForm(Nip_Form_Abstract $form)
    {
        $this->_form = $form;
        return $this;
    }

    /**
     * @return Nip_Form_Abstract
     */
    public function getForm()
    {
        return $this->_form;
    }

    public function getElements()
    {
        if (!$this->_elements) {
            $this->_elements = $this->getForm()->getElements();
        }
        return $this->_elements;
    }

    public function setElements($elements)
    {
        $this->_elements = $elements;
    }

    public function render()
    {
        $return = $this->openTag();
        $return .= $this->renderHidden();

        $renderErrors = $this->getForm()->getOption('render_messages');
        if ($renderErrors !== false) {
            $return .= $this->renderMessages();
        }
        $return .= $this->renderGroups();
        $return .= $this->renderElements();
        $return .= $this->renderButtons();

        $return .= $this->closeTag();
        return $return;
    }

    public function openTag()
    {
        $return = '<form ';
        $atributes = $this->getForm()->getAttribs();
        foreach ($atributes as $name => $value) {
            $return .= $name . '="' . $value . '" ';
        }
        $return .= '>';
        return $return;
    }

    public function renderHidden()
    {
        $hiddenElements = $this->getForm()->findElements(array('type' => 'hidden'));
        $return = '';
        if ($hiddenElements) {
            foreach ($hiddenElements as $element) {
                $return .= $this->renderElement($element);
            }
        }
        return $return;
    }

    public function renderGroups()
    {
        $groups = $this->getForm()->getDisplayGroups();
        $return = '';
        foreach ($groups as $group) {
            $return .= $group->render();
        }
        return $return;
    }

    public function renderElements()
    {
    }

    public function renderLabel($label, $required = false, $error = false)
    {
        if (is_object($label)) {
            $element = $label;
            $label = $element->getLabel();
            $required = $element->isRequired();
            $error = $element->isError();
        }

        $return = '<label class="col-sm-3 ' . ($error ? ' error' : '') . '">';
        $return .= $label. ':';

        if ($required) {
            $return .= '<span class="required">*</span>';
        }

        $return .= "</label>";
        return $return;
    }


    public function renderButtons()
    {
        $return = '';
        $buttons = $this->getForm()->getButtons();
        if ($buttons) {
            $return .= '<div class="form-actions">';
            foreach ($buttons as $button) {
                $return .= $button->render()."\n";
            }
            $return .= '    <div class="clear"></div>';
            $return .= '</div>';
        }
        return $return;
    }

    /**
     * The errors are rendered using the Errors View Helper
     * @return string
     */
    public function renderMessages()
    {
        $return = '';
        $messages = $this->getForm()->getMessages();
        foreach ($messages as $type => $lines) {
            if ($type == "error") {
                $return .= ErrorsHelper::render($lines);

            } else {
                $return .= MessagesHelper::render($lines, $type);
            }
        }
        return $return;
    }

    public function renderElement(Nip_Form_Element_Abstract $element)
    {
        return $element->render();
    }

    public function closeTag()
    {
        $return = '</form>';
        return $return;
    }

    public function getElementRenderer(Nip_Form_Element_Abstract $element)
    {
        $name = $element->getUniqueId();
        if (!$this->_elementsRenderer[$name]) {
            $this->_elementsRenderer[$name] = $this->getNewElementRenderer($element);
        }
        return $this->_elementsRenderer[$name];
    }

    protected function getNewElementRenderer(Nip_Form_Element_Abstract $element)
    {
        $type = $element->getType();
        $name = 'Nip_Form_Renderer_Elements_' . ucfirst($type);
        $renderer = new $name();
        $renderer->setRenderer($this);
        $renderer->setElement($element);
        return $renderer;
    }

    public function getButtonRenderer(Nip_Form_Button_Abstract $button)
    {
        $name = $button->getName();
        if (!$this->_buttonsRenderer[$name]) {
            $this->_buttonsRenderer[$name] = $this->getNewButtonRenderer($button);
        }
        return $this->_buttonsRenderer[$name];
    }

    protected function getNewButtonRenderer(Nip_Form_Button_Abstract $button)
    {
        $type = $button->getType();
        $name = 'Nip_Form_Renderer_Button_' . ucfirst($type);
        $renderer = new $name();
        $renderer->setRenderer($this);
        $renderer->setItem($button);
        return $renderer;
    }

}