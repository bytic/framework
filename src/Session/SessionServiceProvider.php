<?php

namespace Nip\Session;

use Nip\Container\ServiceProviders\Providers\AbstractSignatureServiceProvider;
use Nip\Session\Middleware\StartSession;

/**
 * Class MailServiceProvider
 * @package Nip\Mail
 */
class SessionServiceProvider extends AbstractSignatureServiceProvider
{
    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->registerSessionManager();
        $this->getContainer()->share(StartSession::class);
    }

    /**
     * Register the session manager instance.
     *
     * @return void
     */
    protected function registerSessionManager()
    {
        $this->getContainer()->share('session', SessionManager::class);
    }

    /**
     * @inheritdoc
     */
    public function provides()
    {
        return ['session'];
    }
}
