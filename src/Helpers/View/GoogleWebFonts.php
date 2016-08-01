<?php

/**
 * Nip Framework
 *
 * @category   Nip
 * @copyright  2009 Nip Framework
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 * @version    SVN: $Id: Doctype.php 14 2009-04-13 11:24:22Z victor.stanciu $
 */

class Nip_Helper_View_GoogleWebFonts extends Nip_Helper_View_Abstract {
    
    protected $_fontSelected = array();
    protected $_fontStrings = array(
        'Open+Sans' => 'Open+Sans:400,300,600,700,800,300italic,400italic,700italic,800italic:latin,latin-ext',
        'Open+Sans+Condensed' => 'Open+Sans+Condensed:300,700,300italic:latin,latin-ext',
        'Kaushan+Script' => 'Kaushan+Script::latin,latin-ext',
    );

    public function __toString() {
        $return = '';
        if (count($this->_fontSelected)) {
            $return .= '<script type="text/javascript">';
            $return .= 'WebFontConfig = { google: { families: [ "';
            $families = array();
            foreach ($this->_fontSelected as $fontName => $fontOptions) {
                $family = $this->renderFontVariable($fontName, $fontOptions);
                if ($family) {
                    $families[] = $family;
                }
            }
            $return .= implode('","', $families);
            $return .= '"] } };';
            $return .= '(function() {
                var wf = document.createElement("script");
                wf.src = ("https:" == document.location.protocol ? "https" : "http") +
                  "://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js";
                wf.type = "text/javascript";
                wf.async = "true";
                var s = document.getElementsByTagName("script")[0];
                s.parentNode.insertBefore(wf, s);
              })(); 
            </script>';
        }
        return $return;
    }


    public function add($fontName) 
    {
        $this->_fontSelected[$fontName] = $fontName;
        return $this;
    }
    
    public function renderFontVariable($fontName, $fontOptions)
    {
        return $this->_fontStrings[$fontName];
    }

        /**
     * Singleton
     *
     * @return Nip_Helper_View_GoogleWebFonts
     */
    static public function instance() {
        static $instance;
        if (!($instance instanceof self)) {
            $instance = new self();
        }
        return $instance;
    }
}