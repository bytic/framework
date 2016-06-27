<?php

/**
 * Nip Framework
 *
 * @category   Nip
 * @copyright  2009 Nip Framework
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 * @version    SVN: $Id: Abstract.php 14 2009-04-13 11:24:22Z victor.stanciu $
 */

abstract class Nip_Helper_View_Abstract extends Nip\Helpers\AbstractHelper {

    protected $_view;

    public function setView(Nip_View $view) {
        $this->_view = $view;
    }

    public function getView() {
        if (!$this->_view) {
            $this->_view = Nip_View::instance();
        }
        return $this->_view;
    }

}