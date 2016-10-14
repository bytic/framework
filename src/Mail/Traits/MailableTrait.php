<?php

namespace Nip\Mail\Traits;

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

        return $mailer->send($message);
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
