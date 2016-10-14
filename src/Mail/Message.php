<?php

namespace Nip\Mail;

use Swift_Message;
use Swift_Mime_Message;

/**
 * Class Message
 * @package Nip\Mail
 */
class Message extends Swift_Message implements Swift_Mime_Message
{

    protected $mergeTags = [];

    /**
     * @return array
     */
    public function getMergeTags()
    {
        return $this->mergeTags;
    }

    /**
     * @param array $mergeTags
     */
    public function setMergeTags($mergeTags)
    {
        $this->mergeTags = $mergeTags;
    }
}
