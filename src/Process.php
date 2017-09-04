<?php

namespace Nip;

class Process
{

    protected $_pid;
    protected $_command;

    public function __construct($command = false)
    {
        if ($command) {
            $this->_command = $command;
        }
    }

    public function run()
    {
        $command = "nohup $this->_command > /dev/null 2>&1 & echo $!";
        $this->_pid = shell_exec($command);

        return $this;
    }

    public function running()
    {
        exec("ps -p $this->_pid", $output);
        return isset($output[1]);
    }

    public function stop()
    {
        exec("kill $this->_pid");
        return !$this->running();
    }

    public function setPID($pid)
    {
        $this->_pid = $pid;
    }

    public function getPID()
    {
        return $this->_pid;
    }

}