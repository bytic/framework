<?php

namespace Nip\Config;

/**
 * Class ConfigAwareTrait
 * @package Nip\Config
 */
trait ConfigAwareTrait
{
    /**
     * @var Config|Config|null
     */
    protected $config = null;

    /**
     * Get the container.
     *
     * @return Config
     */
    public function getConfig()
    {
        if ($this->config == null) {
            $this->initConfig();
        }

        return $this->config;
    }

    /**
     * Set a container.
     *
     * @param  Config $config
     * @return $this
     */
    public function setConfig($config)
    {
        $this->config = $config;

        return $this;
    }

    public function initConfig()
    {
        if (app()->has('config')) {
            $config = app()->get('config');
        } else {
            $config = $this->newConfig();
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
}
