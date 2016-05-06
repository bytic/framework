<?php

class Nip_Console_Plugin
{

    protected $_label;
    protected $_enabled = true;

    public function __construct($label)
    {
        $this->_label = $label;
    }

    public function getLabel()
    {
        return $this->_label;
    }

    public function getLabelSlug()
    {
        preg_match_all("/[a-z0-9]+/i", $this->_label, $chunks);
        $return = strtolower(implode("-", $chunks[0]));
        return $return;
    }

    public function setEnabled($enabled)
    {
        $this->_enabled = $enabled;
    }
}