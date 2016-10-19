<?php

namespace Nip;

use Nip\Config\Config;
use Nip\Staging\Stage;
use Nip\Utility\Traits\SingletonTrait;

/**
 * Class Staging
 * @package Nip
 */
class Staging
{

    use SingletonTrait;

    /**
     * @var Stage
     */
    protected $stage;


    /**
     * @var Stage[]
     */
    protected $stages;

    /**
     * @var Config
     */
    protected $config;

    protected $publicStages = ['production', 'staging', 'demo'];

    protected $testingStages = ['local'];

    /**
     * @return Stage
     */
    public function getStage()
    {
        if (!$this->stage) {
            $stage = $this->determineStage();
            $this->updateStage($stage);
        }

        return $this->stage;
    }

    /**
     * @return string
     */
    public function determineStage()
    {
        $stage = $this->determineStageFromConf();
        if ($stage) {
            return $stage;
        }

        $stage = $this->determineStageFromHOST();
        if ($stage) {
            return $stage;
        }

        return 'local';
    }

    /**
     * @return bool
     */
    public function determineStageFromConf()
    {
        if ($this->getConfig()->has('STAGE') && $this->getConfig()->get('STAGE')->has('current')) {
            return $this->getConfig()->get('STAGE')->get('current');
        }

        return false;
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        if (!$this->config) {
            $this->config = $this->initConfig();
        }

        return $this->config;
    }

    /**
     * @return Config
     */
    public function initConfig()
    {
        $config = new Config();
        if ($this->hasConfigFile('staging.ini')) {
            $config->mergeFile($this->getConfigFolder().'staging.ini');
        }

        if ($this->hasConfigFile('stage.ini')) {
            $config->mergeFile($this->getConfigFolder().'stage.ini');
        }

        return $config;
    }

    /**
     * @param $file
     * @return bool
     */
    protected function hasConfigFile($file)
    {
        return is_file($this->getConfigFolder().$file);
    }

    /**
     * @return null
     */
    protected function getConfigFolder()
    {
        return defined('CONFIG_PATH') ? CONFIG_PATH : null;
    }

    /**
     * @return bool|int|string
     */
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

    /**
     * @return array
     */
    public function getStages()
    {
        if (!$this->stages) {
            $stageObj = $this->getConfig()->get('HOSTS');
            if ($stageObj) {
                $this->stages = get_object_vars($stageObj);

                if (is_array($this->stages)) {
                    foreach ($this->stages as &$stage) {
                        if (strpos($stage, ',')) {
                            $stage = array_map("trim", explode(',', $stage));
                        } else {
                            $stage = [trim($stage)];
                        }
                    }
                }
            } else {
                $this->stages = [];
            }
        }

        return $this->stages;
    }

    /**
     * @param $key
     * @param $host
     * @return int
     */
    public function matchHost($key, $host)
    {
        return preg_match('/^'.strtr($key, ['*' => '.*', '?' => '.?']).'$/i',
            $host);
    }

    /**
     * @param $name
     * @return $this
     */
    public function updateStage($name)
    {
        $this->stage = $this->newStage($name);

        return $this;
    }

    /**
     * @param $name
     * @return Stage
     */
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

    /**
     * @param $name
     * @return bool
     */
    public function isInPublicStages($name)
    {
        return in_array($name, $this->publicStages);
    }

    /**
     * @param $name
     * @return bool
     */
    public function isInTestingStages($name)
    {
        return in_array($name, $this->testingStages);
    }
}
