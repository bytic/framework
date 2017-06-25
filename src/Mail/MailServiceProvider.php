<?php

namespace Nip\Mail;

use Nip\Container\ServiceProviders\Providers\AbstractSignatureServiceProvider;

/**
 * Class MailServiceProvider
 * @package Nip\Mail
 */
class MailServiceProvider extends AbstractSignatureServiceProvider
{

    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->registerTransport();
        $this->registerMailer();
    }

    protected function registerTransport()
    {
        $transportManager = new TransportManager();
        $this->getContainer()->share('mailer.transport', $transportManager->create());
    }

    protected function registerMailer()
    {
        $transport = $this->getContainer()->get('mailer.transport');
        $mailer = new Mailer($transport);
        $this->getContainer()->share('mailer', $mailer);
    }

    /**
     * @inheritdoc
     */
    public function provides()
    {
        return ['mailer', 'mailer.transport'];
    }
}
