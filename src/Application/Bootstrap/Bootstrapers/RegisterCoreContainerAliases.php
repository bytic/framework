<?php

namespace Nip\Application\Bootstrap\Bootstrapers;

use Nip\Application;
use Nip\Container\Container;
use Nip\Http\Kernel\Kernel;
use Nip\Http\Kernel\KernelInterface;

/**
 * Class RegisterCoreContainerAliases
 * @package Nip\Application\Bootstrap\Bootstrapers
 */
class RegisterCoreContainerAliases extends AbstractBootstraper
{
    /**
     * Bootstrap the given application.
     *
     * @param Application $app
     * @return void
     */
    public function bootstrap(Application $app)
    {
        /** @var Container $container */
        $container = $app->getContainer();

        $container->share('app', $app);
        $container->share('app', $container);
        $container->share('kernel.http', Kernel::class);

        $container->alias('kernel.http', KernelInterface::class);
    }
}
