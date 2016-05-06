<?php

namespace Nip\Logger\Adapter;

class Console extends AdapterAbstract {

    /**
     * @var Console_Plugin
     */
    protected $_console;

    public function __construct() {
        $console = new \Nip_Console_Plugin_Logger("Log");
        $console->setLogger($this);
        
        \Nip_Console::instance()->plugIn($console);
    }

    public function output() {
        return null;
    }
}