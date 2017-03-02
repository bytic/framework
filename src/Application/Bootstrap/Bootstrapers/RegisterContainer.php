<?php

namespace Nip\Application\Bootstrap\Bootstrapers;

use Nip\Application;
use Nip\Container\Container;

class RegisterContainer extends AbstractBootstraper
{
    /**
     * Bootstrap the given application.
     *
     * @param Application $app
     * @return void
     */
    public function bootstrap(Application $app)
    {

        $app->initContainer();

        $app->share('app', $app);
        $app->share(Container::class, $app);
        $app->share('kernel', $app);
    }
}