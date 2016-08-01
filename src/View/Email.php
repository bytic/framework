<?php

namespace Nip\View;

use Nip\Request;
use Nip\View;

class Email extends View
{

	protected $_layout = "/layouts/email";

    /**
     * @var \Nip_Mailer|null
     */
    protected $_mail = null;

	public function __construct()
	{
		$this->initMailer();
	}

    public function initMailer()
    {
        $this->_mail = new \Nip_Mailer();
    }

	public function initBasePath()
    {
		$this->setBasePath(MODULES_PATH . Request::instance()->getModuleName() . '/views/');
    }

	/**
	 * @param string $host
	 * @param string $username
	 * @param string $password
	 * @return $this
	 */
	public function authSMTP($host, $username, $password)
	{
		$this->_mail->authSMTP($host, $username, $password);
		return $this;
	}

	/**
	 * Sets flag to show SMTP debugging information
	 * @return $this
	 */
	public function debugSMTP()
	{
		$this->_mail->debugSMTP();
		return $this;
	}

	/**
	 * @param string $address
	 * @param string|bool $name
	 * @return $this
	 */
	public function setFrom($address, $name = false)
	{
		$this->_mail->setFrom($address, $name);
		return $this;
	}

	/**
	 * @param string $address
	 * @param string|bool $name
	 * @return $this
	 */
	public function addTo($address, $name = false)
	{
		$this->_mail->addTo($address, $name);
		return $this;
	}

	/**
	 * @param string $address
	 * @param string $name
	 * @return $this
	 */
	public function addBCC($address, $name = '')
	{
		$this->_mail->addBCC($address, $name);
		return $this;
	}

	/**
	 * @param string $address
	 * @param string|bool $name
	 * @return $this
	 */
	public function addReplyTo($address, $name = false)
	{
		$this->_mail->addReplyTo($address, $name);
		return $this;
	}

	/**
	 * @return $this
	 */
	public function clearAllRecipients()
	{
		$this->_mail->clearAllRecipients();
		return $this;
	}

	/**
	 * @param string $subject
	 * @return $this
	 */
	public function setSubject($subject)
	{
		$this->_mail->setSubject($subject);
		return $this;
	}

	/**
	 * Adds attachment
	 *
	 * @param string $path
	 * @param string $name
	 * @return $this
	 */
	public function addAttachment($path, $name = '')
	{
		$this->_mail->addAttachment($path, $name);
		return $this;
	}

	public function send()
	{
		if (!$this->getBody()) {
			$this->setBody($this->load($this->_layout, array(), true));
		}
		$this->_mail->setAltBody($this->getBody());

		return $this->_mail->send();
	}

	public function setLayout($layout)
	{
		$this->_layout = $layout;
	}

	public function setBody($body)
	{
		$this->_mail->setBody($body);
		return $this;
	}

	public function getBody()
	{
		return $this->_mail->getBody();
	}
}