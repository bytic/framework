<?php

namespace Nip\Mail\Traits;

use Nip\Mail\Mailer;
use Nip\Mail\Message;

/**
 * Class MailableTrait.
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
        $recipients = $mailer->send($message);
        $this->afterSend($mailer, $message, $recipients);

        return $recipients;
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
        $this->buildMailMessageCustomArgs($message);

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
     * @param Mailer  $mailer
     * @param Message $message
     */
    protected function beforeSend($mailer, $message)
    {
    }

    /**
     * @param Mailer  $mailer
     * @param Message $message
     * @param int     $recipients
     */
    protected function afterSend($mailer, $message, $recipients)
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

    /**
     * @param Message $message
     */
    abstract public function buildMailMessageCustomArgs(&$message);
}
