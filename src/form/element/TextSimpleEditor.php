<?php
class Nip_Form_Element_TextSimpleEditor extends Nip_Form_Element_Texteditor {

    protected $_type = 'textSimpleEditor';

    protected $_allowedTags = array("a", "b", "br", "p", "img", "small", "span", "strong" );
    protected $_allowedAttributes = array("align", "src", "href", "target");

}