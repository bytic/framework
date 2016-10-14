<?php

namespace Nip\Mail\Models\Mailable;

use Nip\Mail\Message;
use Nip\Mail\Traits\MailableTrait;

/**
 * Class RecordTrait
 * @package Nip\Mail\Models\Mailable
 *
 */
trait RecordTrait
{
    use MailableTrait;

    /**
     * @param Message $message
     */
    public function buildMailMessageFrom(&$message)
    {
        $message->setFrom($this->getFrom());
    }

    /**
     * @return string
     */
    abstract public function getFrom();

    /**
     * @param Message $message
     */
    public function buildMailMessageRecipients(&$message)
    {
        foreach (['to', 'cc', 'bcc', 'replyTo'] as $type) {
            $method = 'get'.ucfirst($type).'s';
            $recipients = method_exists($this, $method) ? $this->{$method}() : $this->{$type};
            foreach ($recipients as $recipient) {
                $message->{'add'.ucfirst($type)}($recipient['address'], $recipient['name']);
            }
        }
    }

    /**
     * @param Message $message
     */
    public function buildMailMessageSubject(&$message)
    {
        $message->setSubject($this->getCompiledSubject());
    }

    /**
     * @return string
     */
    abstract public function getCompiledSubject();

    /**
     * @param Message $message
     */
    public function buildMailMessageBody(&$message)
    {
        $message->setBody($this->getCompiledBody());
    }

    /**
     * @return string
     */
    abstract public function getCompiledBody();

    /**
     * @param Message $message
     */
    public function buildMailMessageAttachments(&$message)
    {
        $message->setMergeTags($this->getMergeTags());
    }

    /**
     * @return array
     */
    abstract public function getMergeTags();

    /**
     * @param Message $message
     */
    public function buildMailMessageMergeTags(&$message)
    {
    }

    /**
     * @return string
     */
    abstract public function getTos();
}
