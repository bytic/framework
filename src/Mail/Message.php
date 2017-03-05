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
    protected $custom_args = [];

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

    /**
     * @return array
     */
    public function getCustomArgs()
    {
        return $this->custom_args;
    }

    /**
     * @param array $custom_args
     */
    public function setCustomArgs($custom_args)
    {
        $this->custom_args = $custom_args;
    }

    /**
     * @param $key
     * @param $value
     */
    public function addCustomArg($key, $value)
    {
        $this->custom_args[$key] = $value;
    }
}
