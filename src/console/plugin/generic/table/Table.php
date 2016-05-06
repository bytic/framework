<?php

class Nip_Console_Plugin_Generic_Table extends Nip_Console_Plugin implements Nip_Console_Plugin_Interface
{

    protected $_data = array();

    public function setData($data = array())
    {
        $this->_data = $data;
    }

    public function getData()
    {
        return $this->_data;
    }

    public function output()
    {
        $table = $this->getData();
        if ($table) {
            $labels = array_keys(reset($table));
        }

        include(dirname(__FILE__) . '/index.php');
    }

    public function getLabel()
    {
        return parent::getLabel() . ' (<span style="color: #333">' . count($this->getData()) . '</span>)';
    }
}