<?php

class Nip_Auth extends Record
{

	protected $_authenticated = false;

	public function authenticate($request = array())
	{
		if ($request) {
			$this->email = clean($request['email']);
			$this->password = clean($request['password']);
		}

		$query = $this->_getAuthenticateQuery();
		$user = $this->getManager()->findOneByQuery($query);

		if ($user) {
			$this->writeData($user->toArray());
			
			$this->id_session = session_id();
			$this->last_login = date(DATE_DB);
			$this->save();

			$this->authenticated(true);

			$_SESSION[$this->_getSessionVar()]['id'] = $this->id;
		}

		return $this->authenticated();
	}

	public function deauthenticate()
	{
		unset($_SESSION[$this->_getSessionVar()]);
		$this->authenticated(false);
		return $this;
	}

	public function authenticated($value = null)
	{
		if (!is_null($value)) {
			$this->_authenticated = $value;
		}
		return $this->_authenticated;
	}

	public function updatePassword()
	{
		$this->password = Nip_Helper_Passwords::instance()->hash($this->new_password);
		$this->save();
		return $this;
	}

	public function recoverPassword()
	{
		$user = $this->getManager()->findOneByEmail($this->email);
		$this->writeData($user->toArray());

		$this->generatePassword()->updatePassword();
		$this->sendRecoverPasswordMail();
	}

	public function generatePassword()
	{
		$this->new_password = Nip_Helper_Passwords::instance()->generate(8, false, true, true, false);
		return $this;
	}

	/**
	 * @return Nip_DB_Query_Select
	 */
	protected function _getAuthenticateQuery()
	{
		$query = $this->getManager()->newQuery();

		$query->where("email = ?", $this->email);
		$query->where("password = ?", Nip_Helper_Passwords::instance()->hash($this->password));

		return $query;
	}

	protected function _getSessionVar()
	{
		return $this->getManager()->getModel();
	}

}