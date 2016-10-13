<?php

namespace Nip\Mail;

use Nip\Container\ServiceProvider\AbstractSignatureServiceProvider;

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
    }

    /**
     * @inheritdoc
     */
    public function provides()
    {
        return ['mailer', 'mailer.transport'];
    }
}