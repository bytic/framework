<?php

class Nip_Helper_View_TinyMCE extends Nip_Helper_View_Abstract
{

	protected $_enabled = false;

	public function setEnabled($enabled = true)
	{
		$this->_enabled = $enabled;
	}

	public function init()
	{
		if ($this->_enabled) {
			$this->getView()->Scripts()->setPack(false)
				->add('tinymce/jquery.tinymce.min', 'tinymce')
				->add('tinymce/tinymce.min', 'tinymce')
				->add('tinymce/init', 'tinymce');
		}

		return $this->getView()->Scripts()->render('tinymce');
	}

	/**
	 * Singleton
	 *
	 * @return Nip_Helper_View_TinyMCE
	 */
	static public function instance()
	{
		static $instance;
		if (!($instance instanceof self)) {
			$instance = new self();
		}
		return $instance;
	}

}