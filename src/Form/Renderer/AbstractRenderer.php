<?php

namespace Nip\Form\Renderer;

use Nip\Form\AbstractForm;
use Nip\Helpers\View\Errors as ErrorsHelper;
use Nip\Helpers\View\Messages as MessagesHelper;
use Nip_Form_Button_Abstract;
use Nip_Form_Element_Abstract as AbstractElement;
use Nip_Form_Renderer_Button_Abstract as AbstractButtonRenderer;
use Nip_Form_Renderer_Elements_Abstract as AbstractElementRenderer;

/**
 * Class AbstractRenderer
 * @package Nip\Form\Renderer
 */
abstract class AbstractRenderer
{
    protected $form;

    protected $elements;
    protected $elementsRenderer;

    protected $buttonsRenderer = [];

    /**
     * AbstractRenderer constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return array
     */
    public function getElements()
    {
        if (!$this->elements) {
            $this->elements = $this->getForm()->getElements();
        }

        return $this->elements;
    }

    /**
     * @param $elements
     */
    public function setElements($elements)
    {
        $this->elements = $elements;
    }

    /**
     * @return AbstractForm
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param AbstractForm $form
     * @return $this
     */
    public function setForm(AbstractForm $form)
    {
        $this->form = $form;

        return $this;
    }

    /**
     * @return string
     */
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

    /**
     * @return string
     */
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

    /**
     * @return string
     */
    public function renderHidden()
    {
        $hiddenElements = $this->getForm()->findElements(['type' => 'hidden']);
        $return = '';
        if ($hiddenElements) {
            foreach ($hiddenElements as $element) {
                $return .= $this->renderElement($element);
            }
        }

        return $return;
    }

    /**
     * @param AbstractElement $element
     * @return mixed
     */
    public function renderElement(AbstractElement $element)
    {
        return $element->render();
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

    /**
     * @return string
     */
    public function renderGroups()
    {
        $groups = $this->getForm()->getDisplayGroups();
        $return = '';
        foreach ($groups as $group) {
            $return .= $group->render();
        }

        return $return;
    }

    /**
     * @return string
     */
    public function renderElements()
    {
        return '';
    }

    /**
     * @return string
     */
    public function renderButtons()
    {
        $return = '';
        $buttons = $this->getForm()->getButtons();
        if ($buttons) {
            $return .= '<div class="form-actions">';
            foreach ($buttons as $button) {
                $return .= $button->render() . "\n";
            }
            $return .= '    <div class="clear"></div>';
            $return .= '</div>';
        }

        return $return;
    }

    /**
     * @return string
     */
    public function closeTag()
    {
        $return = '</form>';

        return $return;
    }

    /**
     * @param string|AbstractElement $label
     * @param bool $required
     * @param bool $error
     * @return string
     */
    public function renderLabel($label, $required = false, $error = false)
    {
        if (is_object($label)) {
            $element = $label;
            $label = $element->getLabel();
            $required = $element->isRequired();
            $error = $element->isError();
        }

        $return = '<label class="col-sm-3 ' . ($error ? ' error' : '') . '">';
        $return .= $label . ':';

        if ($required) {
            $return .= '<span class="required">*</span>';
        }

        $return .= "</label>";

        return $return;
    }

    /**
     * @param AbstractElement $element
     * @return mixed
     */
    public function getElementRenderer(AbstractElement $element)
    {
        $name = $element->getUniqueId();
        if (!isset($this->elementsRenderer[$name])) {
            $this->elementsRenderer[$name] = $this->getNewElementRenderer($element);
        }

        return $this->elementsRenderer[$name];
    }

    /**
     * @param AbstractElement $element
     * @return mixed
     */
    protected function getNewElementRenderer(AbstractElement $element)
    {
        $type = $element->getType();
        $name = 'Nip_Form_Renderer_Elements_' . ucfirst($type);
        /** @var AbstractElementRenderer $renderer */
        $renderer = new $name();
        $renderer->setRenderer($this);
        $renderer->setElement($element);

        return $renderer;
    }

    /**
     * @param Nip_Form_Button_Abstract $button
     * @return mixed
     */
    public function getButtonRenderer(Nip_Form_Button_Abstract $button)
    {
        $name = $button->getName();
        if (!isset($this->buttonsRenderer[$name])) {
            $this->buttonsRenderer[$name] = $this->getNewButtonRenderer($button);
        }

        return $this->buttonsRenderer[$name];
    }

    /**
     * @param Nip_Form_Button_Abstract $button
     * @return mixed
     */
    protected function getNewButtonRenderer(Nip_Form_Button_Abstract $button)
    {
        $type = $button->getType();
        $name = 'Nip_Form_Renderer_Button_' . ucfirst($type);
        /** @var AbstractButtonRenderer $renderer */
        $renderer = new $name();
        $renderer->setRenderer($this);
        $renderer->setItem($button);

        return $renderer;
    }
}
