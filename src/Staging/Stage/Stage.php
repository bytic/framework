<?php

namespace Nip\Staging\Stage;

use Nip\Config\Config;
use Nip\Request;
use Nip\Staging\Staging;

/**
 * Class Stage
 * @package Nip\Staging\Stage
 */
class Stage
{
    protected $manager;

    protected $name;

    protected $type = null;

    protected $hosts;

    protected $host;

    protected $baseURL;

    protected $projectDIR;

    /**
     * @var Config
     */
    protected $config;

    public function init()
    {
        $hosts = $this->getConfig()->get('HOST.url');

        if (strpos($hosts, ',')) {
            $hosts = array_map("trim", explode(',', $hosts));
        } else {
            $hosts = [trim($hosts)];
        }
        $this->setHosts($hosts);
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        if (!$this->config) {
            $this->initConfig();
        }

        return $this->config;
    }

    /**
     * @param Config $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    public function initConfig()
    {
        $config = $this->newConfig();
        if ($this->hasConfigFile()) {
            $config->mergeFile($this->getConfigPath());
        }
        $this->setConfig($config);
    }

    /**
     * @return Config
     */
    public function newConfig()
    {
        return new Config();
    }

    /**
     * @param $hosts
     * @return $this
     */
    public function setHosts($hosts)
    {
        $this->hosts = $hosts;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return bool
     */
    public function isCurrent()
    {
        foreach ($this->hosts as $host) {
            if (preg_match('/^' . strtr($host, ['*' => '.*', '?' => '.?']) . '$/i', $_SERVER['SERVER_NAME'])) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string
     */
    public function getBaseURL()
    {
        if (!$this->baseURL) {
            $this->baseURL = $this->getHTTP() . $this->getHost() . $this->getProjectDir();
        }

        return $this->baseURL;
    }

    /**
     * @return string
     */
    public function getHTTP()
    {
        $https = false;
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") {
            $https = true;
        }

        return "http" . ($https ? "s" : "") . "://";
    }

    /**
     * @return mixed|string
     */
    public function getHost()
    {
        if (!$this->host) {
            if ($this->getConfig()->has('HOST.automatic') && $this->getConfig()->get('HOST.automatic') === false) {
                $this->host = reset($this->hosts);
            }

            if (!$this->host) {
                $this->host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST']
                    : 'localhost';
            }
        }

        return $this->host;
    }

    /**
     * @return string
     */
    public function getProjectDir()
    {
        if (!$this->projectDIR) {
            $this->projectDIR = $this->initProjectDir();
        }

        return $this->projectDIR;
    }

    /**
     * @param $dir
     */
    public function setProjectDir($dir)
    {
        $this->projectDIR = $dir;
    }

    /**
     * @return string
     */
    public function initProjectDir()
    {
        $request = new Request();

        return $request->path();
    }

    /**
     * @return bool
     */
    public function inProduction()
    {
        return $this->name == 'production';
    }

    /**
     * @return bool
     */
    public function isPublic()
    {
        return !$this->isAuthorized() && $this->getManager()->isInPublicStages($this->getType());
    }

    /**
     * @return bool
     */
    public function isAuthorized()
    {
        return isset($_COOKIE['authorized']) && $_COOKIE['authorized'] === 'true';
    }

    /**
     * @return Staging
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * @param Staging $manager
     * @return $this
     */
    public function setManager($manager)
    {
        $this->manager = $manager;

        return $this;
    }

    /**
     * @return null
     */
    public function getType()
    {
        if ($this->type === null) {
            $this->initType();
        }

        return $this->type;
    }

    public function initType()
    {
        $config = $this->getConfig();
        if (isset($config->STAGE) && isset($config->STAGE->type)) {
            $this->type = $config->STAGE->type;
        } else {
            $this->type = $this->name;
        }
    }

    /**
     * @return bool
     */
    public function inTesting()
    {
        return $this->isAuthorized() || $this->getManager()->isInTestingStages($this->getType());
    }

    public function doAuthorize()
    {
        setcookie('authorized', 'true', time() + 60 * 60 * 24, '/');
    }

    /**
     * @return bool
     */
    protected function hasConfigFile()
    {
        return is_file($this->getConfigPath());
    }

    /**
     * @return string
     */
    protected function getConfigPath()
    {
        return $this->getConfigFolder() . $this->name . '.ini';
    }

    /**
     * @return null
     */
    protected function getConfigFolder()
    {
        return defined('CONFIG_STAGING_PATH') ? CONFIG_STAGING_PATH : null;
    }
}
