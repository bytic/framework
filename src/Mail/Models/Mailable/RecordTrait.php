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
            if (is_array($recipients)) {
                foreach ($recipients as $address => $name) {
                    $message->{'add'.ucfirst($type)}($address, $name);
                }
            }
        }
    }

    /**
     * @param Message $message
     */
    public function buildMailMessageSubject(&$message)
    {
        $message->setSubject($this->getSubject());
    }

    /**
     * @return string
     */
    abstract public function getSubject();

    /**
     * @param Message $message
     */
    public function buildMailMessageBody(&$message)
    {
        $message->setBody($this->getBody(), 'text/html');
    }

    /**
     * @return string
     */
    abstract public function getBody();

    /**
     * @param Message $message
     */
    public function buildMailMessageAttachments(&$message)
    {
    }

    /**
     * @param Message $message
     */
    public function buildMailMessageMergeTags(&$message)
    {
        $message->setMergeTags($this->getMergeTags());
    }

    /**
     * @return array
     */
    abstract public function getMergeTags();

    /**
     * @return string
     */
    abstract public function getTos();
}
