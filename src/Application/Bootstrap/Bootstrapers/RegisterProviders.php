<?php

namespace Nip\Application\Bootstrap\Bootstrapers;

use Nip\Application;

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
        $app->registerConfiguredProviders();
    }
}
