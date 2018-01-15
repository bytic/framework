<?php

class Nip_Form_Element_TextSimpleEditor extends Nip_Form_Element_Texteditor
{
    protected $_type = 'textSimpleEditor';

    protected $allowedTags = ['a', 'b', 'br', 'p', 'img', 'small', 'span', 'strong'];
    protected $allowedAttributes = ['align', 'src', 'href', 'target'];
}
