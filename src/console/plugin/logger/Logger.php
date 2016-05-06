<?php
class Console_Plugin_Logger extends Console_Plugin implements Console_Plugin_Interface {

    /* @var $_logger Logger_Adapter */
    protected $_logger;

    public function output() {
        $events = $this->getLogger()->getEvents();
        include(dirname(__FILE__) . '/index.php');
    }

    /**
     * @return Logger_Adapter
     */
    public function getLogger() {
        return $this->_logger;
    }

    public function setLogger(Logger_Adapter $logger) {
        $this->_logger = $logger;
    }

    public function getLabel() {
        return parent::getLabel() . ' (<span style="color: #333">'. count($this->getLogger()->getEvents()) .'</span>)';
    }

    public function setEnabled($enabled) {
        parent::setEnabled($enabled);

        switch ($enabled) {
            case true:
                set_error_handler(array(Logger::instance(), "errorHandler"), E_ALL ^ E_NOTICE);
                break;
            case false:
                restore_error_handler();
                break;
        }
    }
}