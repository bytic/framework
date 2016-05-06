<?php
class Logger_Adapter {

    protected $_events;

    public function addEvent(Logger_Event $event) {
        $this->_events[] = $event;
    }

    public function getEvents() {
        return $this->_events;
    }

    public function clearEvents() {
        return $this->_events = array();
    }
}