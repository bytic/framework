<?php

namespace Nip\Utility;

/**
 * Class Text
 * @package Nip\Utility
 */
class Text
{

    /**
     * @param $str
     * @param array $replace
     * @param string $delimiter
     * @return mixed|string
     */
    public static function toAscii($str, $replace = [], $delimiter = '-')
    {
        if (!empty($replace)) {
            $str = str_replace((array) $replace, ' ', $str);
        }

        $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
        $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
        $clean = strtolower(trim($clean, '-'));
        $clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);

        return $clean;
    }
}
