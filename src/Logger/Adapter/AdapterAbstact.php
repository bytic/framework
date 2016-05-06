<?php

namespace Nip\Logger\Adapter;

class AdapterAbstract implements AdapterInterface
{

    protected $_events;

    public function addEvent(\Nip\Logger\Event $event)
    {
        $this->_events[] = $event;
    }

    public function getEvents()
    {
        return $this->_events;
    }

    public function clearEvents()
    {
        return $this->_events = array();
    }

    public function output()
    {
    }
}