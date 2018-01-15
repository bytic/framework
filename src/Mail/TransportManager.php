<?php

namespace Nip\Mail;

use Nip\Config\ConfigAwareTrait;
use Nip\Mail\Transport\AbstractTransport;
use Nip\Mail\Transport\SendgridTransport;
use Swift_SmtpTransport as SmtpTransport;

/**
 * Class TransportManager.
 */
class TransportManager
{
    use ConfigAwareTrait;

    /**
     * @return AbstractTransport
     */
    public function create()
    {
        return $this->createSendgridTransport();
    }

    /**
     * Create an instance of the Mailgun Swift Transport driver.
     *
     * @return SendgridTransport
     */
    protected function createSendgridTransport()
    {
        $config = $this->getConfig();

        $transport = new SendgridTransport();
        $transport->setApiKey($config->get('SENDGRID.key'));

        return $transport;
    }

    /**
     * Create an instance of the SMTP Swift Transport driver.
     *
     * @return SmtpTransport
     */
    protected function createSmtpTransport()
    {
        $config = $this->app['config']['mail'];
        // The Swift SMTP transport instance will allow us to use any SMTP backend
        // for delivering mail such as Sendgrid, Amazon SES, or a custom server
        // a developer has available. We will just pass this configured host.
        $transport = SmtpTransport::newInstance(
            $config['host'], $config['port']
        );
        if (isset($config['encryption'])) {
            $transport->setEncryption($config['encryption']);
        }
        // Once we have the transport we will check for the presence of a username
        // and password. If we have it we will set the credentials on the Swift
        // transporter instance so that we'll properly authenticate delivery.
        if (isset($config['username'])) {
            $transport->setUsername($config['username']);
            $transport->setPassword($config['password']);
        }
        if (isset($config['stream'])) {
            $transport->setStreamOptions($config['stream']);
        }

        return $transport;
    }
}
