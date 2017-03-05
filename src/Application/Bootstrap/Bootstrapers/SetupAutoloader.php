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
     */
    public function bootstrap(Application $app)
    {
        AutoLoader::registerHandler($app->getAutoLoader());

        $app->getAutoLoader()->setCachePath(
            $app->storagePath() . DIRECTORY_SEPARATOR
            . 'cache' . DIRECTORY_SEPARATOR . "autoloader" . DIRECTORY_SEPARATOR
        );

        $app->setupAutoLoaderPaths();

//        if ($this->getStaging()->getStage()->inTesting()) {
        $app->getAutoLoader()->getClassMapLoader()->setRetry(true);
//        }
    }
}
