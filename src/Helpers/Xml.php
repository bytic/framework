<?php

class Nip_Helper_Xml extends Nip\Helpers\AbstractHelper
{

    public function format($string)
    {
        $doc = new DOMDocument('1.0');
        $doc->preserveWhiteSpace = false;
        $doc->loadXML($string);
        $doc->formatOutput = true;
        return $doc->saveXML();
    }

    /**
     * Singleton
     *
     * @return self
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