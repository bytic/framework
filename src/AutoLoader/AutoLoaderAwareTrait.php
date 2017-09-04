<?php

namespace Nip\AutoLoader;

/**
 * Class AutoLoaderAwareTrait
 * @package Nip\AutoLoader
 */
trait AutoLoaderAwareTrait
{
    /**
     * @var AutoLoader|null
     */
    protected $autoLoader = null;

    /**
     * @return AutoLoader
     */
    public function getAutoLoader()
    {
        if ($this->autoLoader === null) {
            $this->initAutoLoader();
        }

        return $this->autoLoader;
    }

    /**
     * @param AutoLoader $autoLoader
     * @return $this
     */
    public function setAutoLoader($autoLoader = false)
    {
        $this->autoLoader = $autoLoader;

        return $this;
    }

    protected function initAutoLoader()
    {
        $this->setAutoLoader($this->newAutoLoader());
    }

    /**
     * @return AutoLoader
     */
    protected function newAutoLoader()
    {
        return app()->get('autoloader');
    }
}
