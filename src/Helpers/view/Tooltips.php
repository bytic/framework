<?php

/**
 * Nip Framework
 *
 * @category   Nip
 * @copyright  2009 Nip Framework
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 * @version    SVN: $Id: Tooltips.php 14 2009-04-13 11:24:22Z victor.stanciu $
 */

class Nip_Helper_View_Tooltips extends Nip_Helper_View_Abstract {

	private $tooltips = array();

	/**
	 * Adds a tooltip item to the queue
	 *
	 * @param string $id
	 * @param string $content
	 */
	public function addItem($id, $content, $title = false) {
		$this->tooltips[$id] = new Nip_Helper_View_Tooltips_Item($id, $content, $title);
	}


	/**
	 * Returns xHTML-formatted tooltips
	 *
	 * @return string
	 */
	public function render() {
		$return = '';
		if ($this->tooltips) {
			foreach ($this->tooltips as $tooltip) {
				$return .= $tooltip->render();
			}
		}
		return $return;
	}


	/**
	 * Singleton
	 *
	 * @return Nip_Helper_View_Tooltips
	 */
	static public function instance() {
		static $instance;
		if (!($instance instanceof self)) {
			$instance = new self();
		}
		return $instance;
	}
}