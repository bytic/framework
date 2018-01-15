<?php

namespace Nip\Dispatcher;

use Nip\Container\ServiceProvider\AbstractSignatureServiceProvider;

/**
 * Class MailServiceProvider.
 */
class DispatcherServiceProvider extends AbstractSignatureServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->registerDispatcher();
    }

    protected function registerDispatcher()
    {
        $dispatcher = self::newDispatcher();
        $this->getContainer()->singleton('dispatcher', $dispatcher);
    }

    /**
     * @return Dispatcher
     */
    public static function newDispatcher()
    {
        return new Dispatcher();
    }

    /**
     * {@inheritdoc}
     */
    public function provides()
    {
        return ['dispatcher'];
    }
}
