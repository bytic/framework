<?php

namespace Nip\Mail\Transport;

use Html2Text\Html2Text;
use Nip\Mail\Message;
use SendGrid;
use SendGrid\Attachment;
use SendGrid\Content;
use SendGrid\Email;
use SendGrid\Mail;
use SendGrid\Personalization;
use SendGrid\ReplyTo;
use Swift_Attachment;
use Swift_Image;
use Swift_Mime_Message as MessageInterface;
use Swift_MimePart;
use Swift_TransportException;

/**
 * Class SendgridTransport
 * @package Nip\Mail\Transport
 */
class SendgridTransport extends AbstractTransport
{
    /** @var string|null */
    protected $apiKey;

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

        $sg = $this->createApi();

        /** @noinspection PhpUndefinedMethodInspection */
        /** @var SendGrid\Response $response */
        $response = $sg->client->mail()->send()->post($this->getMail());

        if ($response->statusCode() == '202') {
            return 1;
        }
//            echo $response->statusCode();
//            echo '-----------';
//            echo $response->body();
//            echo '-----------';
//            echo $response->headers();
//            die('----------');
//            return $response->body().$response->headers();

        return 0;
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
            $email = new Email($name, $address);
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
            $bodyText = (new Html2Text($bodyHtml))->getText();
        }

        foreach ($message->getChildren() as $child) {
            if ($child instanceof Swift_Image) {
                $images[] = [
                    'type' => $child->getContentType(),
                    'name' => $child->getId(),
                    'content' => base64_encode($child->getBody()),
                ];
            } elseif ($child instanceof Swift_Attachment && !($child instanceof Swift_Image)) {
                $this->addAttachment($child);
            } elseif ($child instanceof Swift_MimePart && $this->supportsContentType($child->getContentType())) {
                if ($child->getContentType() == "text/html") {
                    $bodyHtml = $child->getBody();
                } elseif ($child->getContentType() == "text/plain") {
                    $bodyText = $child->getBody();
                }
            }
        }

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

    /**
     * @param Swift_Attachment $attachment
     */
    protected function addAttachment($attachment)
    {
        $sgAttachment = new Attachment();
        $sgAttachment->setContent(base64_encode($attachment->getBody()));
        $sgAttachment->setType($attachment->getContentType());
        $sgAttachment->setFilename($attachment->getFilename());
        $sgAttachment->setDisposition("attachment");
        $sgAttachment->setContentID($attachment->getId());
        $this->getMail()->addAttachment($sgAttachment);
    }

    /**
     * @return SendGrid
     * @throws Swift_TransportException
     */
    protected function createApi()
    {
        if ($this->getApiKey() === null) {
            throw new Swift_TransportException('Cannot create instance of \Mandrill while API key is NULL');
        }


        $sg = new SendGrid($this->getApiKey());

        return $sg;
    }

    /**
     * @return null|string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @param string $apiKey
     * @return $this
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;

        return $this;
    }
}
