<?php

namespace Nip\Staging;

use Nip\Request;
use Nip\Staging;

class Stage
{
    protected $_manager;
    protected $_name;
    protected $_hosts;
    protected $_host;
    protected $_baseURL;
    protected $_projectDIR;
    protected $_config;

    public function init()
    {
        $hosts = $this->getConfig()->HOST->url;

        if (strpos($hosts, ',')) {
            $hosts = array_map("trim", explode(',', $hosts));
        } else {
            $hosts = array(trim($hosts));
        }
        $this->setHosts($hosts);
    }

    public function getName()
    {
        return $this->_name;
    }

    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }

    /**
     * @return Staging
     */
    public function getManager()
    {
        return $this->_manager;
    }

    /**
     * @param Staging $manager
     */
    public function setManager($manager)
    {
        $this->_manager = $manager;
        return $this;
    }


    public function getType()
    {
        return $this->getConfig()->STAGE->type;
    }

    public function isCurrent()
    {
        foreach ($this->_hosts as $host) {
            if (preg_match('/^' . strtr($host, array('*' => '.*', '?' => '.?')) . '$/i',
                $_SERVER['SERVER_NAME'])) {
                return true;
            }
        }
        return false;
    }

    public function setHosts($hosts)
    {
        $this->_hosts = $hosts;
        return $this;
    }

    public function getHost()
    {
        if (!$this->_host) {
            if (isset($this->getConfig()->HOST->automatic) && !$this->getConfig()->HOST->automatic) {
                $this->_host = reset($this->_hosts);
            }

            if (!$this->_host) {
                $this->_host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST']
                    : 'localhost';
            }
        }

        return $this->_host;
    }

    public function getBaseURL()
    {
        if (!$this->_baseURL) {
            $this->_baseURL = $this->getHTTP() . $this->getHost() . $this->getProjectDir();
        }

        return $this->_baseURL;
    }

    public function getHTTP()
    {
        $https = false;
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") {
            $https = true;
        }
        return "http" . ($https ? "s" : "") . "://";
    }

    public function getProjectDir()
    {
        if (!$this->_projectDIR) {
            $this->_projectDIR = $this->initProjectDir();
        }

        return $this->_projectDIR;
    }

    public function initProjectDir()
    {
        $request = new Request();
        return $request->getHttp()->getPathInfo();
    }

    public function setProjectDir($dir)
    {
        $this->_projectDIR = $dir;
    }

    public function getConfig()
    {
        if (!$this->_config) {
            $this->_config = new \Nip_Config();
            if ($this->hasConfigFile()) {
                $this->_config->parse($this->getConfigPath());
            }
        }
        return $this->_config;
    }

    protected function hasConfigFile()
    {
        return is_file($this->getConfigPath());
    }

    protected function getConfigPath()
    {
        return $this->getConfigFolder() . $this->_name . '.ini';
    }

    protected function getConfigFolder()
    {
        return defined('CONFIG_STAGING_PATH') ? CONFIG_STAGING_PATH : null;
    }

    public function inProduction()
    {
        return $this->_name == 'production';
    }

    public function isPublic()
    {
        return !isset ($_SESSION['authorized']) && $this->getManager()->isInPublicStages($this->getName());
    }

    public function inTesting()
    {
        return isset ($_SESSION['authorized']) || $this->getManager()->isInTestingStages($this->getName());
    }
}