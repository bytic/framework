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

        /** @var DebugBar $debugbar */
        $debugbar = $app->get('debugbar');
        $debugbar->enable();
        $debugbar->boot();

        $this->registerMiddleware(DebugbarMiddleware::class);
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

    /**
     * @inheritdoc
     */
    public function provides()
    {
        return ['debugbar'];
    }
}