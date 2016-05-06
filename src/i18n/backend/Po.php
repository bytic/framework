<?php

class Nip_I18n_Backend_Po extends Nip_I18n_Backend_Abstract
{

	protected $_path;

	/**
	 * Sets and binds the text domain
	 *
	 * @param string $path
	 * @return Nip_I18n_Po
	 */
	public function setPath($path)
	{
		$this->_path = $path;

		bindtextdomain("messages", $this->_path);
        bind_textdomain_codeset('messages', 'UTF-8');
		textdomain("messages");

		return $this;
	}

	/**
	 * Returns gettext translation for $slug in $language
	 *
	 * @see http://php.net/gettext
	 * @param string $slug
	 * @param string $language
	 * @return string
	 */
	protected function _translate($slug, $language = false)
	{
		return gettext($slug);
	}

}
