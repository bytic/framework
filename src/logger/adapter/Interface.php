<?php
interface Logger_Adapter_Interface {

    public function addEvent(Logger_Event $event);
    public function output();

}