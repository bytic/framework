<?php

namespace Nip\Application\Bootstrap\Bootstrapers;

use Nip\Application;

/**
 * Class RegisterProviders
 * @package Nip\Application\Bootstrap\Bootstrapers
 */
class RegisterProviders extends AbstractBootstraper
{
    /**
     * Bootstrap the given application.
     *
     * @param Application $app
     * @return void
     */
    public function bootstrap(Application $app)
    {
        $app->registerServices();
//        $app->registerConfiguredProviders();
    }
}