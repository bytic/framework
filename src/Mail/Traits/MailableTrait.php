<?php

namespace Nip\Mail\Traits;

use Nip\Mail\Message;

trait MailableTrait
{

    protected $mailer = null;

    public function send()
    {
        $mailer = $this->getMailer();
        $message = $this->buildMailMessage();
        return $mailer->send($message);
    }

    public function getMailer()
    {
        if ($this->mailer === null) {
            $this->initMailer();
        }
    }

    /**
     * @param null $mailer
     */
    public function setMailer($mailer)
    {
        $this->mailer = $mailer;
    }

    protected function initMailer()
    {
        $this->setMailer($this->newMailer());
    }

    protected function newMailer()
    {

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
        $this->buildMailMessageAttachments($message);
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
    public function buildMailMessageFrom($message)
    {
    }

    /**
     * @param Message $message
     */
    public function buildMailMessageRecipients($message)
    {
    }

    /**
     * @param Message $message
     */
    public function buildMailMessageSubject($message)
    {
    }

    /**
     * @param Message $message
     */
    public function buildMailMessageAttachments($message)
    {
    }
}