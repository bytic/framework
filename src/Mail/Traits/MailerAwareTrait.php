<?php

namespace Nip\Mail\Traits;

use Nip\Mail\Mailer;

/**
 * Class MailerAwareTrait
 * @package Nip\Mail\Traits
 */
trait MailerAwareTrait
{
    /**
     * @var Mailer|null
     */

    protected $mailer = null;

    /**
     * @return Mailer|null
     */
    public function getMailer()
    {
        if ($this->mailer === null) {
            $this->initMailer();
        }

        return $this->mailer;
    }

    /**
     * @param Mailer $mailer
     */
    public function setMailer($mailer)
    {
        $this->mailer = $mailer;
    }

    protected function initMailer()
    {
        $this->setMailer($this->newMailer());
    }

    /**
     * @return Mailer
     */
    protected function newMailer()
    {
        return app('mailer');
    }
}
