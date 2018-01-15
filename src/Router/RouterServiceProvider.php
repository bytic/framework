<?php

namespace Nip\Router;

use Nip\Container\ServiceProvider\AbstractSignatureServiceProvider;

/**
 * Class MailServiceProvider.
 */
class RouterServiceProvider extends AbstractSignatureServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->registerRouter();
    }

    protected function registerRouter()
    {
        $dispatcher = self::newRouter();
        $this->getContainer()->singleton('router', $dispatcher);
    }

    /**
     * @return Router
     */
    public static function newRouter()
    {
        return new Router();
    }

    /**
     * {@inheritdoc}
     */
    public function provides()
    {
        return ['router'];
    }
}
