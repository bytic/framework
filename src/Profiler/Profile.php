<?php

namespace Nip\Profiler;

class Profile
{

    public $id = null;
    public $name = null;
    public $columns = array('type', 'time', 'memory');

    protected $startedMicrotime = null;
    protected $endedMicrotime = null;

    protected $startedMemory = null;
    protected $endedMemory = null;

    protected $time = null;
    protected $memory = null;


    public function __construct($id)
    {
        $this->id = $id;
        $this->name = $id;
        $this->start();
    }

    /**
     * @param null $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }


    public function __get($name)
    {
        return $this->$name;
    }


    public function __set($name, $value)
    {
        $this->$name = $value;
    }


    public function start()
    {
        $this->startedMicrotime = microtime(true);
        $this->startedMemory = memory_get_usage();
    }

    public function end()
    {
        // Ensure that the query profile has not already ended
        if ($this->hasEnded()) {
            return;
        }
        $this->endTimers();
        $this->calculateResources();
    }

    public function endTimers()
    {
        $this->endedMicrotime = microtime(true);
        $this->endedMemory = memory_get_usage();
    }

    public function calculateResources()
    {
        $this->time = $this->calculateElapsedSecs();
        $this->memory = $this->calculateUsedMemory();
    }


    public function hasEnded()
    {
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

    public function calculateElapsedSecs()
    {
        if (null === $this->endedMicrotime) {
            return false;
        }

        return $this->endedMicrotime - $this->startedMicrotime;
    }

    public function getTime()
    {
        return $this->time;
    }


    public function calculateUsedMemory()
    {
        if (null === $this->endedMemory) {
            return false;
        }

        return number_format(($this->endedMemory - $this->startedMemory) / 1024) . ' KB';
    }

    public function getMemory()
    {
        return $this->memory;
    }
}