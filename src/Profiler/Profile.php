<?php

namespace Nip\Profiler;

class Profile {

    public $columns = array('type', 'time', 'memory');

    protected $startedMicrotime = null;
    protected $endedMicrotime   = null;

    protected $startedMemory    = null;
    protected $endedMemory  = null;


    public function __construct($type) {
        $this->type = $type;
        $this->start();
    }


    public function __get($name) {
        return $this->$name;
    }


    public function __set($name, $value) {
        $this->$name = $value;
    }


    public function start() {
        $this->startedMicrotime = microtime(true);
        $this->startedMemory    = memory_get_usage();
    }


    public function end() {
        $this->endedMicrotime   = microtime(true);
        $this->time             = $this->getElapsedSecs();

        $this->endedMemory      = memory_get_usage();
        $this->memory           = $this->getUsedMemory();
    }


    public function hasEnded() {
        return $this->endedMicrotime !== null;
    }

    public function getStartMicrotime()
    {
        return $this->startedMicrotime;
    }

    public function getEndMicrotime()
    {
        return $this->endedMicrotime;
    }

    public function getElapsedSecs() {
        if (null === $this->endedMicrotime) {
            return false;
        }

        return $this->endedMicrotime - $this->startedMicrotime;
    }


    public function getUsedMemory() {
        if (null === $this->endedMemory) {
            return false;
        }

        return number_format(($this->endedMemory - $this->startedMemory) / 1024) . ' KB';
    }
}