<?php

namespace Nip\Dispatcher;

use Nip\Container\ServiceProviders\Providers\AbstractSignatureServiceProvider;

/**
 * Class MailServiceProvider
 * @package Nip\Mail
 */
class DispatcherServiceProvider extends AbstractSignatureServiceProvider
{

    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->registerDispatcher();
    }

    protected function registerDispatcher()
    {
        $dispatcher = self::newDispatcher();
        $this->getContainer()->share('dispatcher', $dispatcher);
    }

    /**
     * @return Dispatcher
     */
    public static function newDispatcher()
    {
        return new Dispatcher();
    }

    /**
     * @inheritdoc
     */
    public function provides()
    {
        return ['dispatcher'];
    }
}
