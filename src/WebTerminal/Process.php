<?php

namespace Nip\WebTerminal;

class Process
{
    protected $_command;
    protected $_verbose = true;

    protected $_process;
    protected $_pipes;

    protected $_return;
    protected $_exitCode;

    public function setCommand($command)
    {
        $this->_command = $command;
    }

    public function run()
    {
        $this->initProcess();
        $this->runProcess();
    }

    public function initProcess()
    {
        $this->_process = proc_open($this->_command, $this->getDescriptorSpec(), $this->_pipes);
    }

    public function getDescriptorSpec()
    {
        return array(
            0 => array("pipe", "r"),  // stdin
            1 => array("pipe", "w"),  // stdout -> we use this
            2 => array("pipe", "w")   // stderr
        );
    }

    public function runProcess()
    {
        if (is_resource($this->_process)) {
            // $pipes now looks like this:
            // 0 => writeable handle connected to child stdin
            // 1 => readable handle connected to child stdout
            // Any error output will be appended to /tmp/error-output.txt

            $this->output();
            $this->closeProcess();
        }
    }

    public function output()
    {
        while (!feof($this->_pipes[1])) {
            $returnLine = fgets($this->_pipes[1], 1024);
            $this->_return .= $returnLine;
            if (strlen($returnLine) == 0) {
                break;
            }
            if ($this->isVerbose()) {
                echo $returnLine."\n";
            }
            ob_flush();
            flush();
        }
    }

    public function isVerbose()
    {
        return $this->_verbose === true;
    }

    public function setVerbose($verbose)
    {
        $this->_verbose = $verbose === false ? false : true;
    }

    public function closeProcess()
    {
        fclose($this->_pipes[1]);
        $this->_exitCode = proc_close($this->_process);
    }

    public function isError()
    {
        return $this->_exitCode !== 0;
    }

    public function getExitCode()
    {
        return $this->_exitCode;
    }

    public function getReturn()
    {
        return trim($this->_return);
    }
}