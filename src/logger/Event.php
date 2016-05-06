<?php
class Logger_Event {
    
    protected $_type;
    protected $_data;
    protected $_backtrace;

    public function setData($data) {
        $this->_data = $data;
    }

    public function setBacktrace($backtrace) {
        foreach ($backtrace as $step) {
            unset($step['object']);
            $this->_backtrace[] = $step;
        }
    }

    static public function getNew($type = false) {
        switch ($type) {
            case Logger::EVENT_ERROR:
                require_once 'event/Error.php';

                $event = new Logger_Event_Error();
                break;
            case Logger::EVENT_INFO:
            default:
                require_once 'event/Info.php';
                $event = new Logger_Event_Info();
                break;
        }

        return $event;
    }

    public function getType() {
        return $this->_type;
    }

    public function getData() {
        return $this->_data;
    }

    public function getBacktrace() {
        return $this->_backtrace;
    }
}