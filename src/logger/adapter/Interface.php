<?php

namespace Nip\Logger\Adapter;

interface AdapterInterface {

    public function addEvent(\Nip\Logger\Event $event);
    public function output();

}