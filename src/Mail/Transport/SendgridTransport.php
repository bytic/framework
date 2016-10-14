<?php

namespace Nip\Mail\Transport;

use Nip\Mail\Message;
use SendGrid\Content;
use SendGrid\Email;
use SendGrid\Mail;
use SendGrid\Personalization;
use SendGrid\ReplyTo;
use Swift_Attachment;
use Swift_Image;
use Swift_Mime_Message as MessageInterface;
use Swift_MimePart;

/**
 * Class SendgridTransport
 * @package Nip\Mail\Transport
 */
class SendgridTransport extends AbstractTransport
{

    /**
     * @var null|Mail|MessageInterface
     */
    protected $mail = null;

    /**
     * {@inheritdoc}
     */
    public function send(\Swift_Mime_Message $message, &$failedRecipients = null)
    {
        $this->initMail();

//        $mail->addCategory($this->type);
//        $mail->addCustomArg("id_email", (string)$this->id);

        $this->populateSenders($message);
        $this->populatePersonalization($message);
        $this->populateContent($message);
    }

    public function initMail()
    {
        $this->setMail(new Mail());
    }

    /**
     * @param Message|MessageInterface $message
     */
    protected function populateSenders($message)
    {
        $from = $message->getFrom();
        foreach ($from as $address => $name) {
            $email = new Email($address, $name);
            $this->getMail()->setFrom($email);

            $reply_to = new ReplyTo($address);
            $this->getMail()->setReplyTo($reply_to);
        }
    }

    /**
     * @return null|Mail
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * @param null|Mail $mail
     */
    public function setMail($mail)
    {
        $this->mail = $mail;
    }

    /**
     * @param Message|MessageInterface $message
     */
    protected function populatePersonalization($message)
    {
        $emailsTos = $message->getTo();
        $i = 0;
        foreach ($emailsTos as $emailTo => $nameTo) {
            $personalization = $this->generatePersonalization($emailTo, $nameTo, $message, $i);
            $this->getMail()->addPersonalization($personalization);
            $i++;
        }
    }

    /**
     * @param $emailTo
     * @param $nameTo
     * @param Message $message
     * @param $i
     * @return Personalization
     */
    protected function generatePersonalization($emailTo, $nameTo, $message, $i)
    {
        $personalization = new Personalization();

        $email = new Email($nameTo, $emailTo);
        $personalization->addTo($email);

        $personalization->setSubject($message->getSubject());

        $mergeTags = $message->getMergeTags();
        foreach ($mergeTags as $varKey => $value) {
            if (is_array($value)) {
                $value = $value[$i];
            }
            $value = (string)$value;
            $personalization->addSubstitution('{{'.$varKey.'}}', $value);
        }

        return $personalization;
    }

    /**
     * @param Message|MessageInterface $message
     */
    protected function populateContent($message)
    {
        $contentType = $this->getMessagePrimaryContentType($message);

        $bodyHtml = $bodyText = null;

        if ($contentType === 'text/plain') {
            $bodyText = $message->getBody();
        } else {
            $bodyHtml = $message->getBody();
            $bodyText = (new \Html2Text\Html2Text($bodyHtml))->getText();
        }

        foreach ($message->getChildren() as $child) {
            if ($child instanceof Swift_Image) {
                $images[] = [
                    'type' => $child->getContentType(),
                    'name' => $child->getId(),
                    'content' => base64_encode($child->getBody()),
                ];
            } elseif ($child instanceof Swift_Attachment && !($child instanceof Swift_Image)) {
                $attachments[] = [
                    'type' => $child->getContentType(),
                    'name' => $child->getFilename(),
                    'content' => base64_encode($child->getBody()),
                ];
            } elseif ($child instanceof Swift_MimePart && $this->supportsContentType($child->getContentType())) {
                if ($child->getContentType() == "text/html") {
                    $bodyHtml = $child->getBody();
                } elseif ($child->getContentType() == "text/plain") {
                    $bodyText = $child->getBody();
                }
            }
        }

        var_dump($attachments);
        die();

        $content = new Content("text/plain", $bodyText);
        $this->getMail()->addContent($content);

        $content = new Content("text/html", $bodyHtml);
        $this->getMail()->addContent($content);
    }

    /**
     * @param MessageInterface $message
     * @return string
     */
    protected function getMessagePrimaryContentType(MessageInterface $message)
    {
        $contentType = $message->getContentType();
        if ($this->supportsContentType($contentType)) {
            return $contentType;
        }
        // SwiftMailer hides the content type set in the constructor of Swift_Mime_Message as soon
        // as you add another part to the message. We need to access the protected property
        // _userContentType to get the original type.
        $messageRef = new \ReflectionClass($message);
        if ($messageRef->hasProperty('_userContentType')) {
            $propRef = $messageRef->getProperty('_userContentType');
            $propRef->setAccessible(true);
            $contentType = $propRef->getValue($message);
        }

        return $contentType;
    }

    /**
     * @param string $contentType
     * @return bool
     */
    protected function supportsContentType($contentType)
    {
        return in_array($contentType, $this->getSupportedContentTypes());
    }

    /**
     * @return array
     */
    protected function getSupportedContentTypes()
    {
        return [
            'text/plain',
            'text/html',
        ];
    }
}
