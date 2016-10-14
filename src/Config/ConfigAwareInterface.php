<?php

namespace Nip\Config;

/**
 * Interface ConfigAwareInterface
 * @package Nip\Config
 */
interface ConfigAwareInterface
{
    /**
     * Set a container
     *
     * @param Config $config
     */
    public function setConfig($config);

    /**
     * Get the container
     *
     * @return Config
     */
    public function getConfig();
}
