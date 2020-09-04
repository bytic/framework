<?php

use function Nip\url;

if (!function_exists('pr')) {
    function pr($mixed)
    {
        echo '<pre>';
        print_r($mixed);
        echo '</pre>';
    }
}

/**
 * @param $input
 * @return string
 */
function encode_url($input)
{
    $chars = [
        '&#x102;' => 'a',
        '&#x103;' => 'a',
        '&#xC2;' => 'A',
        '&#xE2;' => 'a',
        '&#xCE;' => 'I',
        '&#xEE;' => 'i',
        '&#x218;' => 'S',
        '&#x219;' => 's',
        '&#x15E;' => 'S',
        '&#x15F;' => 's',
        '&#x21A;' => 'T',
        '&#x21B;' => 't',
        '&#354;' => 'T',
        '&#355;' => 't',
        '&#039;' => '',
    ];

    foreach ($chars as $i => $v) {
        $chars[html_entity_decode($i, ENT_QUOTES, 'UTF-8')] = $v;
    }

    $input = strtr($input, $chars);

    preg_match_all('/[a-z0-9]+/i', $input, $chunks);
    $return_ = strtolower(implode('-', $chunks[0]));

    return $return_;
}

/**
 * @return string
 */
function current_url()
{
    return defined('CURRENT_URL') ? CURRENT_URL : url()->current();
}

/**
 * Transforms a date's string representation into $format.
 *
 * @param string $format
 * @param string|int $datetime
 *
 * @return string/bool
 */
function _date($datetime, $format = false)
{
    $format = $format ? $format : Nip\locale()->getOption(['time', 'dateFormat']);
    $time = is_numeric($datetime) ? $datetime : strtotime($datetime);

    return $time ? date($format, $time) : false;
}

/**
 * Transforms a date's string representation into $format.
 *
 * @param string $format
 * @return string/bool
 */
function _strtotime($date, $format = false)
{
    $format = $format ? $format : Nip\locale()->getOption(['time', 'dateStringFormat']);
    $dateArray = date_parse_from_format($date, $format);

    return mktime($dateArray['tm_hour'], $dateArray['tm_min'], $dateArray['tm_sec'], 1 + $dateArray['tm_mon'],
        $dateArray['tm_mday'], 1900 + $dateArray['tm_year']);
}

/**
 * Transforms a date's string representation into $format.
 *
 * @param string $format
 * @param string|int $datetime
 *
 * @return string/bool
 */
function _strftime($datetime, $format = false)
{
    if ($datetime && strpos($datetime, '0000-00-00') === false) {
        $format = $format ? $format : Nip\locale()->getOption(['time', 'dateStringFormat']);
        if (is_numeric($datetime)) {
            $time = $datetime;
        } else {
            $time = strtotime($datetime);
        }

        if ($time !== false && $time !== -1) {
            return iconv('ISO-8859-2', 'ASCII//TRANSLIT', strftime($format, $time));
        }
    }

    return false;
}

if (!function_exists('pluck')) {
    function pluck($array, $property)
    {
        return \Nip\HelperBroker::get('Arrays')->pluck($array, $property);
    }
}

/**
 * @return string
 */
function max_upload()
{
    $post_max_size = ini_get('post_max_size');
    $upload_max_filesize = ini_get('upload_max_filesize');

    $unit = strtoupper(substr($post_max_size, -1));
    $multiplier = ($unit == 'M' ? 1048576 : ($unit == 'K' ? 1024 : ($unit == 'G' ? 1073741824 : 1)));
    $post_max_size = ((int) $post_max_size) * $multiplier;

    $unit = strtoupper(substr($upload_max_filesize, -1));
    $multiplier = ($unit == 'M' ? 1048576 : ($unit == 'K' ? 1024 : ($unit == 'G' ? 1073741824 : 1)));
    $upload_max_filesize = ((int) $upload_max_filesize) * $multiplier;

    return round((min($post_max_size, $upload_max_filesize) / 1048576), 2) . 'MB';
}

if (!function_exists('json_decode')) {

    /**
     * @param $json
     * @param bool $assoc
     * @param int $n
     * @param int $state
     * @param int $waitfor
     * @return array|float|int|mixed|null|stdClass|string
     */
    function json_decode(
        $json,
        $assoc = false, /* emu_args */
        $n = 0,
        $state = 0,
        $waitfor = 0
    ) {

        //-- result var
        $val = null;
        static $lang_eq = ["true" => true, "false" => false, "null" => null];
        static $str_eq = [
            "n" => "\012",
            "r" => "\015",
            "\\" => "\\",
            '"' => '"',
            'f' => "\f",
            'b' => "\b",
            't' => "\t",
            '/' => '/',
        ];

        //-- flat char-wise parsing
        for (/* n */; $n < strlen($json); /* n */) {
            $c = $json[$n];

            //-= in-string
            if ($state === '"') {
                if ($c == '\\') {
                    $c = $json[++$n];
                    // simple C escapes
                    if (isset($str_eq[$c])) {
                        $val .= $str_eq[$c];
                    } // here we transform \uXXXX Unicode (always 4 nibbles) references to UTF-8
                    elseif ($c == 'u') {
                        // read just 16bit (therefore value can't be negative)
                        $hex = hexdec(substr($json, $n + 1, 4));
                        $n += 4;
                        // Unicode ranges
                        if ($hex < 0x80) { // plain ASCII character
                            $val .= chr($hex);
                        } elseif ($hex < 0x800) {   // 110xxxxx 10xxxxxx
                            $val .= chr(0xC0 + $hex >> 6) . chr(0x80 + $hex & 63);
                        } elseif ($hex <= 0xFFFF) { // 1110xxxx 10xxxxxx 10xxxxxx
                            $val .= chr(0xE0 + $hex >> 12) . chr(0x80 + ($hex >> 6) & 63) . chr(0x80 + $hex & 63);
                        }
                        // other ranges, like 0x1FFFFF=0xF0, 0x3FFFFFF=0xF8 and 0x7FFFFFFF=0xFC do not apply
                    }

                    // no escape, just a redundant backslash
                    //@COMPAT: we could throw an exception here
                    else {
                        $val .= '\\' . $c;
                    }
                } // end of string
                elseif ($c == '"') {
                    $state = 0;
                } // yeeha! a single character found!!!!1!
                else /* if (ord($c) >= 32) */ { //@COMPAT: specialchars check - but native json doesn't do it?
                    $val .= $c;
                }
            } //-> end of sub-call (array/object)
            elseif ($waitfor && (strpos($waitfor, $c) !== false)) {
                return [$val, $n]; // return current value and state
            } //-= in-array
            elseif ($state === ']') {
                list($v, $n) = json_decode($json, 0, $n, 0, ',]');
                $val[] = $v;
                if ($json[$n] == ']') {
                    return [$val, $n];
                }
            } //-= in-object
            elseif ($state === '}') {
                list($i, $n) = json_decode($json, 0, $n, 0, ':'); // this allowed non-string indicies
                list($v, $n) = json_decode($json, 0, $n + 1, 0, ',}');
                $val[$i] = $v;
                if ($json[$n] == '}') {
                    return [$val, $n];
                }
            } //-- looking for next item (0)
            else {

                //-> whitespace
                if (preg_match("/\s/", $c)) {
                    // skip
                } //-> string begin
                elseif ($c == '"') {
                    $state = '"';
                } //-> object
                elseif ($c == '{') {
                    list($val, $n) = json_decode($json, $assoc, $n + 1, '}', '}');
                    if ($val && $n && !$assoc) {
                        $obj = new stdClass();
                        foreach ($val as $i => $v) {
                            $obj->{$i} = $v;
                        }
                        $val = $obj;
                        unset($obj);
                    }
                } //-> array
                elseif ($c == '[') {
                    list($val, $n) = json_decode($json, $assoc, $n + 1, ']', ']');
                } //-> comment
                elseif (($c == '/') && ($json[$n + 1] == '*')) {
                    // just find end, skip over
                    ($n = strpos($json, '*/', $n + 1)) or ($n = strlen($json));
                } //-> numbers
                elseif (preg_match("#^(-?\d+(?:\.\d+)?)(?:[eE]([-+]?\d+))?#", substr($json, $n), $uu)) {
                    $val = $uu[1];
                    $n += strlen($uu[0]) - 1;
                    if (strpos($val, '.')) {  // float
                        $val = (float) $val;
                    } elseif ($val[0] == '0') {  // oct
                        $val = octdec($val);
                    } else {
                        $val = (int) $val;
                    }
                    // exponent?
                    if (isset($uu[2])) {
                        $val *= pow(10, (int) $uu[2]);
                    }
                } //-> boolean or null
                elseif (preg_match("#^(true|false|null)\b#", substr($json, $n), $uu)) {
                    $val = $lang_eq[$uu[1]];
                    $n += strlen($uu[1]) - 1;
                } //-- parsing error
                else {
                    // PHPs native json_decode() breaks here usually and QUIETLY
                    trigger_error("json_decode: error parsing '$c' at position $n", E_USER_WARNING);

                    return $waitfor ? [null, 1 << 30] : null;
                }
            }//state
            //-- next char
            if ($n === null) {
                return;
            }
            $n++;
        }//for
        //-- final result
        return $val;
    }
}

/**
 * @param $distance
 * @return string
 */
function _htmlDistance($distance)
{
    $integer = intval($distance);
    $decimalPosition = strrpos($distance, '.');
    $decimal = $decimalPosition === false ? false : substr($distance, $decimalPosition);

    return intval($distance) . ($decimal ? '<small>' . $decimal . '</small>' : '');
}
