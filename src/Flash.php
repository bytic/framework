<?php

/**
 * Nip Framework
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category   Nip
 * @copyright  2009 Nip Framework
 * @version    SVN: $Id: Flash.php 14 2009-04-13 11:24:22Z victor.stanciu $
 */

class Nip_Flash {

	protected $previous		= array();
	protected $next			= array();
	protected $session_var	= 'flash-data';


	public function __construct() {
		$this->read();
	}

	public function has($var) {
		return isset($this->previous[trim($var)]) ? true : false;
	}

	public function get($var) {
		return isset($this->previous[trim($var)]) ? $this->previous[trim($var)] : null;
	}


	public function add($var, $value) {
		$this->next[trim($var)] = $value;
		$this->write();
	}


	public function remove($var) {
		unset($this->next[trim($var)]);
		$this->write();
	}


	protected function clear() {
		$this->next = array();
	}


	public function read() {
		$data = $_SESSION[$this->session_var];
		if (!is_null($data)) {
			if (is_array($data)) {
				$this->previous = $data;
			}
			unset($_SESSION[$this->session_var]);
		}
	}


	protected function write() {
		$_SESSION[$this->session_var] = $this->next;
	}


	/**
	 * Returns static instance
	 *
	 * @return Nip_Flash
	 */
	static public function &instance() {
		static $instance;
		if (!($instance instanceof self)) {
			$instance = new self();
		}
		return $instance;
	}
}