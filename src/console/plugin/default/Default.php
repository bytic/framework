<?php
class Console_Plugin_Default extends Console_Plugin implements Console_Plugin_Interface {

    protected $_data = array();

    public function output() {
        $data = $this->getData();
        include(dirname(__FILE__) . '/index.php');
    }

    public function getData() {
        return $this->_data;
    }

    public function log($data) {
        $this->_data[] = $data;
    }

    public function getLabel() {
        return parent::getLabel() . ' (<span style="color: #333">'. count($this->getData()) .'</span>)';
    }
}