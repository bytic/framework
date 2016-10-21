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
     * @param bool|AutoLoader $autoLoader
     * @return $this
     */
    public function setAutoLoader($autoLoader = false)
    {
        $this->autoLoader = $autoLoader;

        return $this;
    }

    public function initAutoLoader()
    {
        if (app()->has('autoloader')) {
            $autoLoader = app()->get('autoloader');
        } else {
            $autoLoader = $this->newDispatcher();
        }
        $this->setAutoLoader($autoLoader);
    }

    /**
     * @return AutoLoader
     */
    protected function newAutoLoader()
    {
        return AutoLoaderServiceProvider::newAutoLoader();
    }
}
