<?php

namespace Nip\Logger;

use Nip\Logger\Manager;

class Event
{

    protected $_type;
    protected $_data;
    protected $_backtrace;

    public function setData($data)
    {
        $this->_data = $data;
    }

    public function setBacktrace($backtrace)
    {
        foreach ($backtrace as $step) {
            unset($step['object']);
            $this->_backtrace[] = $step;
        }
    }

    static public function getNew($type = false)
    {
        switch ($type) {
            case Manager::EVENT_ERROR:
                $event = new Event\Error();
                break;
            case Manager::EVENT_INFO:
            default:
                $event = new Event\Info();
                break;
        }

        return $event;
    }

    public function getType()
    {
        return $this->_type;
    }

    public function getData()
    {
        return $this->_data;
    }

    public function getBacktrace()
    {
        return $this->_backtrace;
    }
}