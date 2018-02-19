<?php

namespace Nip\Application\Bootstrap\Bootstrapers;

use Nip\Application;

/**
 * Class RegisterContainer
 * @package Nip\Application\Bootstrap\Bootstrapers
 */
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
    }
}