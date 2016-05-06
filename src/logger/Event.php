<?php

namespace Nip\Logger;

class Event {
    
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
            case \Nip\Logger::EVENT_ERROR:
                $event = new \Nip\Logger\Event\Error();
                break;
            case \Nip\Logger::EVENT_INFO:
            default:
                $event = new \Nip\Logger\Event\Info();
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