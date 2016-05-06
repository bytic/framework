<?php

/**
 * Nip Framework
 *
 * @category   Nip
 * @copyright  2009 Nip Framework
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 * @version    SVN: $Id: Doctype.php 14 2009-04-13 11:24:22Z victor.stanciu $
 */

class Nip_Helper_View_Doctype extends Nip_Helper_View_Abstract {
    protected $_doctype;

    public function __toString() {
        switch ($this->_doctype) {
            case 'XHTML11'             : return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">';
            case 'XHTML1_STRICT'       : return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
            case 'XHTML1_FRAMESET'     : return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">';
            case 'XHTML_BASIC1'        : return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML Basic 1.0//EN" "http://www.w3.org/TR/xhtml-basic/xhtml-basic10.dtd">';
            case 'HTML4_STRICT'        : return '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">';
            case 'HTML4_LOOSE'         : return '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
            case 'HTML4_FRAMESET'      : return '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">';
            case 'XHTML1_TRANSITIONAL' : return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
            default                    : return '<!DOCTYPE html>';
        }
    }


    public function set($doctype = false) {
        switch ($doctype) {
            case 'XHTML11':
            case 'XHTML1_STRICT':
            case 'XHTML1_TRANSITIONAL':
            case 'XHTML1_FRAMESET':
            case 'XHTML_BASIC1':
            case 'HTML4_STRICT':
            case 'HTML4_LOOSE':
            case 'HTML4_FRAMESET':
                $this->_doctype = $doctype;
                break;
            default:
                throw new PHPException('unknown doctype');
                break;
        }

        return $this;
    }


    /**
     * Singleton
     *
     * @return Nip_Helper_View_Doctype
     */
    static public function instance() {
        static $instance;
        if (!($instance instanceof self)) {
            $instance = new self();
        }
        return $instance;
    }
}