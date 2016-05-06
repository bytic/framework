<?php

require_once dirname(__FILE__) . "/../../console/plugin/logger/Logger.php";

class Logger_Adapter_Console extends Logger_Adapter {

    /**
     * @var Console_Plugin
     */
    protected $_console;

    public function __construct() {
        $console = new Console_Plugin_Logger("Log");
        $console->setLogger($this);
        
        Console::instance()->plugIn($console);
    }

    public function output() {
        return null;
    }
}