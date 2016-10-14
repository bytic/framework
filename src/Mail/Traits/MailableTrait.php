<?php

namespace Nip\Mail\Traits;

use Nip\Mail\Mailer;
use Nip\Mail\Message;

/**
 * Class MailableTrait
 * @package Nip\Mail\Traits
 */
trait MailableTrait
{
    use MailerAwareTrait;

    /**
     * @return int
     */
    public function send()
    {
        $mailer = $this->getMailer();
        $message = $this->buildMailMessage();

        $this->beforeSend($mailer, $message);
        $response = $mailer->send($message);
        $this->afterSend($mailer, $message, $response);

        return $response;
    }

    /**
     * @return Message
     */
    public function buildMailMessage()
    {
        $message = $this->newMailMessage();
        $this->buildMailMessageFrom($message);
        $this->buildMailMessageRecipients($message);
        $this->buildMailMessageSubject($message);
        $this->buildMailMessageBody($message);
        $this->buildMailMessageAttachments($message);
        $this->buildMailMessageMergeTags($message);

        return $message;
    }

    /**
     * @return Message
     */
    public function newMailMessage()
    {
        $message = new Message();

        return $message;
    }

    /**
     * @param Mailer $mailer
     * @param Message $message
     */
    protected function beforeSend($mailer, $message)
    {
    }

    /**
     * @param Mailer $mailer
     * @param Message $message
     * @param $response
     */
    protected function afterSend($mailer, $message, $response)
    {
    }

    /**
     * @param Message $message
     */
    abstract public function buildMailMessageFrom(&$message);

    /**
     * @param Message $message
     */
    abstract public function buildMailMessageRecipients(&$message);

    /**
     * @param Message $message
     */
    abstract public function buildMailMessageSubject(&$message);

    /**
     * @param Message $message
     */
    abstract public function buildMailMessageBody(&$message);

    /**
     * @param Message $message
     */
    abstract public function buildMailMessageAttachments(&$message);

    /**
     * @param Message $message
     */
    abstract public function buildMailMessageMergeTags(&$message);
}
