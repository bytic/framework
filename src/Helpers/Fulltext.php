<?php

/**
 * Nip Framework
 *
 * @category   Nip
 * @copyright  2009 Nip Framework
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 * @version    SVN: $Id: Fulltext.php 14 2009-04-13 11:24:22Z victor.stanciu $
 */

class Nip_Helper_Fulltext extends Nip\Helpers\AbstractHelper {

    public function buildString($keywords, $mode = 'any') {
        $return = "";

        $keywords = explode(" ", $keywords);

        switch ($mode) {
            case "any":
                foreach ($keywords as $item) {
                    $return .= $this->matchNumbers($item).'* ';
                }
            break;

            case "all":
                if (count($keywords) == 1) {
                    $return .=  $this->matchNumbers(reset($keywords)).'* ';
                } else {
                    foreach ($keywords as $item) {
                        $return .= '+'. $this->matchNumbers($item).'* ';
                    }
                }

            break;
                case "exact":
                foreach ($keywords as $item) {
                    $return .= $this->matchNumbers($item).' ';
                }
                $return = '+"'.$return.'"';
            break;
        }

        $return = strtolower(trim($return));

        return $return;
    }


    private function matchNumbers($input) {
        $stripped = array("%", ",", ".");
        $replaced = array("__", "_", "_");

        if (is_numeric(str_replace($stripped, "", $input))) {
            return str_replace($stripped, $replaced, $input);
        } else {
            return $input;
        }
    }


    /**
     * Returns singleton instance
     *
     * @return Nip_Helper_Fulltext
     */
    static public function instance() {
        static $instance;
        if (!($instance instanceof self)) {
            $instance = new self();
        }
        return $instance;
    }
}