<?php

class Nip_View_XML extends Nip_View
{

	public function load($view = false, $variables = array(), $return = false)
	{
		header('Content-type: text/xml');
		return parent::load($view, $variables, $return);
	}

	/**
	 * Singleton
	 *
	 * @return Nip_View_XML
	 */
	public static function instance()
	{
		static $instance;
		if (!($instance instanceof self)) {
			$instance = new self();
		}
		return $instance;
	}

}
