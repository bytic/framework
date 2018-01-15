<?php

namespace Nip\Mail;

use Nip\Container\ServiceProvider\AbstractSignatureServiceProvider;

/**
 * Class MailServiceProvider.
 */
class MailServiceProvider extends AbstractSignatureServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->registerTransport();
        $this->registerMailer();
    }

    protected function registerTransport()
    {
        $transportManager = new TransportManager();
        $this->getContainer()->singleton('mailer.transport', $transportManager->create());
    }

    protected function registerMailer()
    {
        $transport = $this->getContainer()->get('mailer.transport');
        $mailer = new Mailer($transport);
        $this->getContainer()->singleton('mailer', $mailer);
    }

    /**
     * {@inheritdoc}
     */
    public function provides()
    {
        return ['mailer', 'mailer.transport'];
    }
}
