<?php
class Nip_Form_Renderer_Elements_Texteditor extends Nip_Form_Renderer_Elements_Textarea
{
    protected $_editorClass = 'mceAdvanced';

    public function generateElement()
    {
        if (!$this->getElement()->getAttrib('id')) {
            $this->getElement()->setAttrib('id', $this->getElement()->getAttrib('name'));
            $this->getElement()->addClass($this->_editorClass);
        }
        $return = parent::generateElement();
        return $return;
    }

    public function getElementAttribs()
    {
        $attribs = parent::getElementAttribs();
        return $attribs;
    }
}
