<?php

class Nip_Helper_View_Icontext extends Nip_Helper_View_Abstract
{

	protected $_symbols = array(
		'unknown' => 'f016',
		'image' => 'f03e',
		'refresh' => 'f021',
		'chevron_right' => 'f054',
		'twitter' => 'f099',
		'facebook' => 'f09a'
	);

	public function __call($name, $arguments) 
	{
		if (!in_array($name, array_keys($this->_symbols))) {
			$name = 'unknown';
		}
		return '&#x' . $this->_symbols[$name] . ';';
	}

}