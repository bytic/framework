<?php
class Nip_Form_Element_TextMiniEditor extends Nip_Form_Element_Texteditor {

    protected $_type = 'textMiniEditor';

    protected $_allowedTags = array("a", "b", "br", "p", "span", "strong" );
    protected $_allowedAttributes = array("align", "src", "href", "target");

}