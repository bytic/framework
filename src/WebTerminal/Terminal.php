<?php

namespace Nip\WebTerminal;

class Terminal
{
    protected $_OS = null;
    protected $_RunUser = null;

    protected $_requiredBinaries = array();
    protected $_commands = array();

    public function dispatch()
    {
        $this->init();
        $this->run();
        $this->postDispatch();
    }

    public function init()
    {
        $this->initHTML();
        $this->printHeader();
        $this->checkRequiredBinaries();
    }

    public function initHTML()
    {
        require('./Layout/header.html');
    }

    public function printHeader()
    {
        echo '
Checking the environment ...
Running as <b>'.$this->getRunUser().'</b>.
';
    }

    public function getRunUser()
    {
        if ($this->_RunUser === null) {

            $this->_RunUser = trim(shell_exec('whoami'));
        }

        return $this->_RunUser;
    }

    public function checkRequiredBinaries()
    {
        foreach ($this->_requiredBinaries as $binary) {
            $this->checkRequiredBinary($binary);
        }
    }

    public function checkRequiredBinary($binary)
    {
        $shellCommand = $this->getCommand('which').' '.$binary;
        $process = $this->runProcess($shellCommand, false);
        $path = $process->getReturn();
        $this->checkRequiredBinaryPath($path, $shellCommand, $binary);
    }

    public function getCommand($command)
    {
        if ($this->getOS() == 'Windows') {
            switch ($command) {
                case 'which':
                    return 'where';

            }
        }

        return $command;
    }

    public function getOS()
    {
        if ($this->_OS === null) {
            $this->_OS = $this->getRunUser() == 'nt authority\system' ? 'Windows' : 'Linux';
        }

        return $this->_OS;
    }

    public function runProcess($command, $output = true)
    {
        $process = new Process();
        $process->setCommand($command);
        $process->setVerbose($output);
        $process->run();

        if ($process->isError()) {
            $this->printProcessError($process);
            die();
        }

        return $process;
    }

    public function printProcessError(Process $process)
    {
        echo '
<div class="error">
Error encountered!
Stopping the script to prevent possible data loss.
ERROR CODE ['.$process->getExitCode().']
</div>
';
    }

    public function checkRequiredBinaryPath($path, $shellCommand, $binary)
    {
        if ($path == '') {
            die(sprintf('<div class="error">
                    <b>%s</b> not available. It needs to be installed on the server for this script to work.
                    [%s]
                </div>', $binary, $shellCommand));
        } else {
            $version = explode("\n", shell_exec($binary.' --version'));
            printf('<b>%s</b> : %s'."\n", $path, $version[0]);
        }
    }

    public function run()
    {
        $this->runPreCheck();
        $this->printRunHeader();
        $this->runCommands();
    }

    public function runPreCheck()
    {
    }

    public function printRunHeader()
    {
        echo '
Environment OK.
Deploying ['.__DIR__.']
Run Commands on ['.getcwd()."]\n";
    }

    public function runCommands()
    {
        foreach ($this->_commands as $command) {
            $this->runCommand($command);
        }
    }

    public function runCommand($command)
    {
        set_time_limit(300); // Reset the time limit for each command
        $this->printCommand($command);
        echo '<div class="output">';
        $process = $this->runProcess($command);
        echo 'Exit Code ['.$process->getExitCode().']'."\n";
        echo '</div>';
    }

    public function printCommand($command)
    {
        echo '<span class="prompt">$</span> <span class="command">'.$command.'</span>';
    }

    public function postDispatch()
    {
        echo '
Done.
</pre>
</body>
</html>';
    }

    public function setCWD($dir)
    {
        chdir($dir);
    }

    public function addCommand($command)
    {
        $this->_commands[] = $command;

        return $this;
    }

    public function checklCommand($command)
    {
        set_time_limit(300); // Reset the time limit for each command
        $this->printCommand($command);
        echo '<div class="output">';
        $this->runProcess($command);
        echo '</div>';
    }

    public function addRequiredBinaries($required)
    {
        $this->_requiredBinaries[] = $required;

        return $this;
    }
}