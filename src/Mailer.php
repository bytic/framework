<?php

class Nip_Mailer
{

	protected $_mail;

	public function __construct()
	{
		$this->_mail = new PHPMailer();
        $this->_mail->CharSet = "UTF-8";
//		$this->_mail->SMTPDebug = true;

		$config = Nip_Config::instance()->parse(CONFIG_PATH . 'smtp.ini');
		if ($config->SMTP->host) {
            $this->_mail->IsSMTP();
			if ($config->SMTP->username) {
				$this->authSMTP($config->SMTP->host, $config->SMTP->username, $config->SMTP->password);				
			} else {				
                $this->_mail->Host = $config->SMTP->host;
			}
		}

		$this->setFrom($config->EMAIL->from, $config->EMAIL->from_name);
	}

	/**
	 * @param string $host
	 * @param string $username
	 * @param string $password
	 * @return $this
	 */
	public function authSMTP($host, $username, $password)
	{
		$this->_mail->IsSMTP();

		$this->_mail->SMTPAuth = true;
		$this->_mail->Host = $host;
		$this->_mail->Username = $username;
		$this->_mail->Password = $password;

		return $this;
	}
    
    public function IsHTML($bool) {
        $this->_mail->IsHTML($bool);
    }

	/**
	 * Sets flag to show SMTP debugging information
	 * @return $this
	 */
	public function debugSMTP()
	{
		$this->_mail->SMTPDebug = PHP_INT_MAX;
		return $this;
	}

	/**
	 * @param string $address
	 * @param string|bool $name
	 * @return $this
	 */
	public function setFrom($address, $name = false)
	{
		$this->_mail->From = $address;
		if ($name) {
			$this->_mail->FromName = $name;
		}
		return $this;
	}

	/**
	 * @param string $address
	 * @param string|bool $name
	 * @return $this
	 */
	public function addTo($address, $name = false)
	{
		$this->_mail->AddAddress($address, $name);
		return $this;
	}

	/**
	 * @param string $address
	 * @param string $name
	 * @return $this
	 */
	public function addBCC($address, $name = '')
	{
		$this->_mail->AddBCC($address, $name);
		return $this;
	}

	/**
	 * @param string $address
	 * @param string $name
	 * @return $this
	 */
	public function addReplyTo($address, $name = false)
	{
		$this->_mail->AddReplyTo($address, $name);
		return $this;
	}

	/**
	 * @return $this
	 */
	public function clearAllRecipients()
	{
		$this->_mail->ClearAllRecipients();
		return $this;
	}

	/**
	 * @param string $subject
	 * @return $this
	 */
	public function setSubject($subject)
	{
		$this->_mail->Subject = $subject;
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
		$this->_mail->AddAttachment($path, $name);
		return $this;
	}

	public function send()
	{
		$return = $this->_mail->Send();

		if (!$return) {
			$return = $this->_mail->ErrorInfo;
		}
		
		return $return;
	}


	public function setBody($body)
	{
		$this->_mail->Body = $body;
		return $this;
	}

	public function getBody()
	{
		return $this->_mail->Body;
	}
   
	public function setAltBody($body)
	{
		$this->_mail->AltBody = $body;
		return $this;
	}

}