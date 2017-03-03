<?php

namespace Nip\Application\Bootstrap\Bootstrapers;

use Nip\Application;
use Nip\Logger\Manager;

/**
 * Class HandleExceptions
 * @package Nip\Application\Bootstrap\Bootstrapers
 */
class HandleExceptions extends AbstractBootstraper
{
    /**
     * Bootstrap the given application.
     *
     * @param Application $app
     * @return void
     */
    public function bootstrap(Application $app)
    {
        /** @var Manager $handler */
        $handler = $app->get(Manager::class);
        $handler->setBootstrap($app);
        $handler->init();

//        if ($app->getStaging()->getStage()->inTesting()) {
//            $app->getDebugBar()->enable();
//            $app->getDebugBar()->addMonolog($app->getLogger()->getMonolog());
//        }
    }

}