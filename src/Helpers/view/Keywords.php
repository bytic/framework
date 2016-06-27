<?php

/**
 * Nip Framework
 *
 * @category   Nip
 * @copyright  2009 Nip Framework
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 * @version    SVN: $Id: Keywords.php 14 2009-04-13 11:24:22Z victor.stanciu $
 */

class Nip_Helper_View_Keywords extends Nip_Helper_View_Abstract {

    private $items;

    public function addItem($item) {
        $this->items[] = strtolower($item);
    }


    /**
     * Returns XHTML formatted breadcrumbs container and elements
     *
     * @return string
     */
    public function render() {
        $return = '';
        if ($this->items) {
            $return = implode(",", $this->items) . ",";
        }
        return $return;
    }


    /**
     * Singleton
     *
     * @return Nip_Helper_View_Keywords
     */
    static public function instance() {
        static $instance;
        if (!($instance instanceof self)) {
            $instance = new self();
        }
        return $instance;
    }
}