<?php

namespace Nip\Mail\Transport;

/**
 * Class SendgridTransport
 * @package Nip\Mail\Transport
 */
class SendgridTransport extends AbstractTransport
{

    /**
     * {@inheritdoc}
     */
    public function send(\Swift_Mime_Message $message, &$failedRecipients = null)
    {

    }
}
