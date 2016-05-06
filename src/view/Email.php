<?php

class Nip_View_Email extends Nip_View
{

	protected $_layout = "/layouts/email";

	public function __construct()
	{
		$this->_mail = new Nip_Mailer();
		$this->setBasePath(MODULES_PATH . Nip_Request::instance()->module . '/views/');
	}

	/**
	 * @param string $host
	 * @param string $username
	 * @param string $password
	 * @return Nip_ViewEmail
	 */
	public function authSMTP($host, $username, $password)
	{
		$this->_mail->authSMTP($host, $username, $password);
		return $this;
	}

	/**
	 * Sets flag to show SMTP debugging information
	 * @return Nip_ViewEmail
	 */
	public function debugSMTP()
	{
		$this->_mail->debugSMTP();
		return $this;
	}

	/**
	 * @param string $address
	 * @param string $name
	 * @return Nip_ViewEmail
	 */
	public function setFrom($address, $name = false)
	{
		$this->_mail->setFrom($address, $name = false);
		return $this;
	}

	/**
	 * @param string $address
	 * @param string $name
	 * @return Nip_ViewEmail
	 */
	public function addTo($address, $name = false)
	{
		$this->_mail->addTo($address, $name);
		return $this;
	}

	/**
	 * @param string $address
	 * @param string $name
	 * @return Nip_ViewEmail
	 */
	public function addBCC($address, $name = '')
	{
		$this->_mail->AddBCC($address, $name);
		return $this;
	}

	/**
	 * @param string $address
	 * @param string $name
	 * @return Nip_ViewEmail
	 */
	public function addReplyTo($address, $name = false)
	{
		$this->_mail->AddReplyTo($address, $name);
		return $this;
	}

	/**
	 * @return Nip_View_Email
	 */
	public function clearAllRecipients()
	{
		$this->_mail->ClearAllRecipients();
		return $this;
	}

	/**
	 * @param string $subject
	 * @return Nip_ViewEmail
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
	 * @return Nip_ViewEmail
	 */
	public function addAttachment($path, $name = '')
	{
		$this->_mail->AddAttachment($path, $name);
		return $this;
	}

	public function send()
	{
		if (!$this->getBody()) {
			$this->setBody($this->load($this->_layout, array(), true));
		}
		$this->_mail->setAltBody($this->getBody());

		return $this->_mail->Send();
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