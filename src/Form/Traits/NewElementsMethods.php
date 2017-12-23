<?php

namespace Nip\Form\Traits;

/**
 * Class NewElementsMethods
 * @package Nip\Form\Traits
 */
trait NewElementsMethods
{

    /**
     * @return \Nip_Form_Element_Select
     */
    public function getNewSelectElement()
    {
        return $this->getNewElement('select');
    }
}
