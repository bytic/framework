<?php

namespace Nip\DebugBar;

use Nip\Container\ServiceProviders\Providers\AbstractSignatureServiceProvider;
use Nip\Container\ServiceProviders\Providers\BootableServiceProviderInterface;
use Nip\DebugBar\Middleware\DebugbarMiddleware;
use Nip\Http\Kernel\Kernel;
use Nip\Http\Kernel\KernelInterface;

/**
 * Class DebugBarServiceProvider
 * @package Nip\DebugBar
 */
class DebugBarServiceProvider extends AbstractSignatureServiceProvider implements BootableServiceProviderInterface
{

    /**
     * @inheritdoc
     */
    public function provides()
    {
        return ['debugbar'];
    }

    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->getContainer()->alias(StandardDebugBar::class, DebugBar::class);

        $this->getContainer()->share('debugbar', function () {
            $debugbar = $this->getContainer()->get(DebugBar::class);
            return $debugbar;
        });
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $app = $this->getContainer()->get('app');

        // If enabled is null, set from the app.debug value
//        $enabled = $this->app['config']->get('debugbar.enabled');

//        if (is_null($enabled)) {
        $enabled = $this->checkAppDebug();
//        }

        if (!$enabled) {
            return;
        }

        /** @var DebugBar $debugBar */
        $debugBar = $app->get('debugbar');
        $debugBar->enable();
        $debugBar->boot();

        $this->registerMiddleware(DebugbarMiddleware::class);
    }

    /**
     * Check the App Debug status
     */
    protected function checkAppDebug()
    {
        return $this->getContainer()->get('config')->get('app.debug');
    }

    /**
     * Register the Debugbar Middleware
     *
     * @param  string $middleware
     */
    protected function registerMiddleware($middleware)
    {
        /** @var Kernel $kernel */
        $kernel = $this->getContainer()->get(KernelInterface::class);
        $kernel->prependMiddleware($middleware);
    }
}
