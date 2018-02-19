<?php

namespace Nip\Application\Bootstrap\Bootstrapers;

use Nip\Application;
use Nip\AutoLoader\AutoLoader;

/**
 * Class SetupAutoloader
 * @package Nip\Application\Bootstrap\Bootstrapers
 */
class SetupAutoloader extends AbstractBootstraper
{
    /**
     * Bootstrap the given application.
     *
     * @param Application $app
     * @return void
     * @throws \Nip\AutoLoader\Exception
     */
    public function bootstrap(Application $app)
    {
        AutoLoader::registerHandler($app->getAutoLoader());

        $app->setupAutoLoaderCache();
        $app->setupAutoLoaderPaths();

        if ($app->getStaging()->getStage()->inTesting()) {
            $app->getAutoLoader()->getClassMapLoader()->setRetry(true);
        }
    }
}