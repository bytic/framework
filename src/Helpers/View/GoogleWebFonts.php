<?php
namespace Nip\Helpers\View;

class GoogleWebFonts extends AbstractHelper {

    protected $_fontSelected = [];
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
            $families = [];
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

    public function renderFontVariable($fontName)
    {
        return $this->_fontStrings[$fontName];
    }

    public function add($fontName)
    {
        $this->_fontSelected[$fontName] = $fontName;

        return $this;
    }
}