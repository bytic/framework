<?php

namespace Nip\Application\Bootstrap\Bootstrapers;

use Nip\Application;

/**
 * Class AbstractBootstraper
 * @package Nip\Application\Bootstrap\Bootstrapers
 */
abstract class AbstractBootstraper implements BootstraperInterface
{
    /**
     * The application instance.
     *
     * @var Application
     */
    protected $app;

    /**
     * @return Application
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * @param Application $app
     */
    public function setApp($app)
    {
        $this->app = $app;
    }
}