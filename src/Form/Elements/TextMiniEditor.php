<?php

class Nip_Form_Element_TextMiniEditor extends Nip_Form_Element_Texteditor
{
    protected $_type = 'textMiniEditor';

    protected $allowedTags = ["a", "b", "br", "p", "span", "strong"];
    protected $allowedAttributes = ["align", "src", "href", "target"];
}
