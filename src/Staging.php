<?php

namespace Nip;

use Nip\Staging\Stage;

class Staging
{

    protected $_stage;
    protected $_stages;
    protected $_config;
    protected $_publicStages = array('production', 'staging', 'demo');
    protected $_testingStages = array('local');

    /**
     * @return Nip\Staging\Stage
     */
    public function getStage()
    {
        if (!$this->_stage) {
            $stage = $this->determineStage();
            $this->updateStage($stage);
        }

        return $this->_stage;
    }

    public function determineStage()
    {
        if ($stage = $this->determineStageFromConf()) {
            return $stage;
        }

        if ($stage = $this->determineStageFromHOST()) {
            return $stage;
        }
        return 'local';
    }

    public function determineStageFromConf()
    {
        if (isset($this->getConfig()->STAGE) && isset($this->getConfig()->STAGE->current)) {
            return $this->getConfig()->STAGE->current;
        }
        return false;
    }

    public function determineStageFromHOST()
    {
        $_stage = false;
        if (isset($_SERVER['SERVER_NAME'])) {
            foreach ($this->getStages() as $stage => $hosts) {
                foreach ($hosts as $host) {
                    if ($this->matchHost($host, $_SERVER['SERVER_NAME'])) {
                        $_stage = $stage;
                        break 2;
                    }
                }
            }
        }
        return $_stage;
    }

    public function matchHost($key, $host)
    {
        return preg_match('/^' . strtr($key, array('*' => '.*', '?' => '.?')) . '$/i',
            $host);
    }

    public function updateStage($name)
    {
        $this->_stage = $this->newStage($name);
        return $this;
    }

    public function newStage($name)
    {
        $stage = new Stage();
        $stage->setManager($this);
        $stage->setName($name);

        $stages = $this->getStages();
        if (isset($stages[$name])) {
            $stage->setHosts($stages[$name]);
        }

        return $stage;
    }

    public function getStages()
    {
        if (!$this->_stages) {
            $stageObj = $this->getConfig()->HOSTS;
            if ($stageObj) {
                $this->_stages = get_object_vars($stageObj);

                if (is_array($this->_stages)) {
                    foreach ($this->_stages as &$stage) {
                        if (strpos($stage, ',')) {
                            $stage = array_map("trim", explode(',', $stage));
                        } else {
                            $stage = array(trim($stage));
                        }
                    }
                }
            } else {
                $this->_stages = array();
            }
        }
        return $this->_stages;
    }

    public function getConfig()
    {
        if (!$this->_config) {
            $this->_config = $this->initConfig();
        }
        return $this->_config;
    }

    public function initConfig()
    {
        $config = new \Nip_Config();
        if ($this->hasConfigFile('staging.ini')) {
            $config->parse($this->getConfigFolder() . 'staging.ini');
        }

        if ($this->hasConfigFile('stage.ini')) {
            $config->parse($this->getConfigFolder() . 'stage.ini');
        }
        return $config;
    }

    protected function hasConfigFile($file)
    {
        return is_file($this->getConfigFolder() . $file);
    }

    protected function getConfigFolder()
    {
        return defined('CONFIG_PATH') ? CONFIG_PATH : null;
    }

    public function isInPublicStages($name)
    {
        return in_array($name, $this->_publicStages);
    }

    public function isInTestingStages($name)
    {
        return in_array($name, $this->_testingStages);
    }

    /**
     * Singleton
     * @return self
     */
    static public function instance()
    {
        static $instance;
        if (!($instance instanceof self)) {
            $instance = new self();
        }
        return $instance;
    }
}