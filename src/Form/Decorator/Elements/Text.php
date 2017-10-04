<?php
class Nip_Form_Decorator_Elements_Text extends Nip_Form_Decorator_Elements_Abstract
{
    protected $_content;

    public function setText($text)
    {
        $this->_content = $text;
        return $this;
    }

    public function generate()
    {
        return $this->_content;
    }
}
