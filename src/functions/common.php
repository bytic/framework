<?php

function encode_url($input)
{
    $chars = array(
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
    );

    foreach ($chars as $i => $v) {
        $chars[html_entity_decode($i, ENT_QUOTES, 'UTF-8')] = $v;
    }

    $input = strtr($input, $chars);

    preg_match_all("/[a-z0-9]+/i", $input, $chunks);
    $return_ = strtolower(implode("-", $chunks[0]));

    return $return_;
}

/**
 * @return string
 */
function current_url()
{
    return defined('CURRENT_URL') ? CURRENT_URL : null;
}

/**
 * Transforms a date's string representation into $format
 *
 * @param string $format
 * @param string|int $datetime
 * @return string/bool
 */
function _date($datetime, $format = false)
{
    $format = $format ? $format : Nip\locale()->getOption(['time', 'dateFormat']);
    $time = is_numeric($datetime) ? $datetime : strtotime($datetime);

    return $time ? date($format, $time) : false;
}

/**
 * Transforms a date's string representation into $format
 *
 * @param string $format
 * @return string/bool
 */
function _strtotime($date, $format = false)
{
    $format = $format ? $format : Nip\locale()->getOption(['time', 'dateStringFormat']);
    $dateArray = strptime($date, $format);

    return mktime($dateArray['tm_hour'], $dateArray['tm_min'], $dateArray['tm_sec'], 1 + $dateArray['tm_mon'],
        $dateArray['tm_mday'], 1900 + $dateArray['tm_year']);
}

/**
 * Transforms a date's string representation into $format
 *
 * @param string $format
 * @param string|int $datetime
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
            return iconv("ISO-8859-2", "ASCII//TRANSLIT", strftime($format, $time));
        }
    }

    return false;
}


if (!function_exists("pluck")) {

    function pluck($array, $property)
    {
        return \Nip\HelperBroker::get('Arrays')->pluck($array, $property);
    }

}

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

function valid_url($input)
{
    return preg_match("|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i", $input);
}


function valid_email($email)
{
    $isValid = true;
    $atIndex = strrpos($email, "@");
    if (is_bool($atIndex) && !$atIndex) {
        $isValid = false;
    } else {
        $domain = substr($email, $atIndex + 1);
        $local = substr($email, 0, $atIndex);
        $localLen = strlen($local);
        $domainLen = strlen($domain);
        if ($localLen < 1 || $localLen > 64) {
            // local part length exceeded
            $isValid = false;
        } else {
            if ($domainLen < 1 || $domainLen > 255) {
                // domain part length exceeded
                $isValid = false;
            } else {
                if ($local[0] == '.' || $local[$localLen - 1] == '.') {
                    // local part starts or ends with '.'
                    $isValid = false;
                } else {
                    if (preg_match('/\.\./', $local)) {
                        // local part has two consecutive dots
                        $isValid = false;
                    } else {
                        if (!preg_match('/^[A-Za-z0-9\-\.]+$/', $domain)) {
                            // character not valid in domain part
                            $isValid = false;
                        } else {
                            if (preg_match('/\.\./', $domain)) {
                                // domain part has two consecutive dots
                                $isValid = false;
                            } else {
                                if (!preg_match('/^(\.|[A-Za-z0-9!#%&`_=\/$\'*+?^{}|~.-])+$/',
                                    str_replace("\\", "", $local))
                                ) {
                                    // character not valid in local part unless
                                    // local part is quoted
                                    if (!preg_match('/^"(\"|[^"])+"$/', str_replace("\\", "", $local))) {
                                        $isValid = false;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        if ($isValid && !(checkdnsrr($domain, "MX") || checkdnsrr($domain, "A"))) {
            // domain not found in DNS
            $isValid = false;
        }
    }

    return $isValid;
}

function valid_cc_number($cc_number)
{
    /* Validate; return value is card type if valid. */
    $card_type = "";
    $card_regexes = array(
        "/^4\d{12}(\d\d\d){0,1}$/" => "visa",
        "/^5[12345]\d{14}$/" => "mastercard",
        "/^3[47]\d{13}$/" => "amex",
        "/^6011\d{12}$/" => "discover",
        "/^30[012345]\d{11}$/" => "diners",
        "/^3[68]\d{12}$/" => "diners",
    );

    foreach ($card_regexes as $regex => $type) {
        if (preg_match($regex, $cc_number)) {
            $card_type = $type;
            break;
        }
    }

    if (!$card_type) {
        return false;
    }

    /*  mod 10 checksum algorithm  */
    $revcode = strrev($cc_number);
    $checksum = 0;

    for ($i = 0; $i < strlen($revcode); $i++) {
        $current_num = intval($revcode[$i]);
        if ($i & 1) {  /* Odd  position */
            $current_num *= 2;
        }
        /* Split digits and add. */
        $checksum += $current_num % 10;
        if ($current_num > 9
        ) {
            $checksum += 1;
        }
    }

    if ($checksum % 10 == 0) {
        return $card_type;
    } else {
        return false;
    }
}

function valid_cnp($cnp)
{
    $const = '279146358279';
    $cnp = trim($cnp);

    preg_match("|^([1256])(\d{2})(\d{2})(\d{2})(\d{6})$|ims", $cnp, $results);
    if (count($results) < 1) {
        return false;
    }

    $mf = $results[1] + 0;
    if ($mf == 5 || $mf == 6) {
        $year_add = 2000;
    } else {
        $year_add = 1900;
    }
    $year = $year_add + $results[2];
    $month = $results[3] + 0;
    $day = $results[4] + 0;

    if (!checkdate($month, $day, $year)) {
        return false;
    }

    $suma = 0;
    for ($i = 0; $i < 12; $i++) {
        $suma += $const[$i] * $cnp[$i];
    }

    $rest = $suma % 11;

    $c13 = $cnp[12] + 0;

    if (!(($rest < 10 && $rest == $c13) || ($rest == 10 && $c13 == 1))) {
        return false;
    }

    return true;
}

if (!function_exists("money_format")) {

    /**
     * @param string $format
     */
    function money_format($format, $number)
    {
        $regex = '/%((?:[\^!\-]|\+|\(|\=.)*)([0-9]+)?' .
            '(?:#([0-9]+))?(?:\.([0-9]+))?([in%])/';
        if (setlocale(LC_MONETARY, 0) == 'C') {
            setlocale(LC_MONETARY, '');
        }
        $locale = localeconv();
        preg_match_all($regex, $format, $matches, PREG_SET_ORDER);
        foreach ($matches as $fmatch) {
            $value = floatval($number);
            $flags = array(
                'fillchar' => preg_match('/\=(.)/', $fmatch[1], $match) ?
                    $match[1] : ' ',
                'nogroup' => preg_match('/\^/', $fmatch[1]) > 0,
                'usesignal' => preg_match('/\+|\(/', $fmatch[1], $match) ?
                    $match[0] : '+',
                'nosimbol' => preg_match('/\!/', $fmatch[1]) > 0,
                'isleft' => preg_match('/\-/', $fmatch[1]) > 0,
            );
            $width = trim($fmatch[2]) ? (int) $fmatch[2] : 0;
            $left = trim($fmatch[3]) ? (int) $fmatch[3] : 0;
            $right = trim($fmatch[4]) ? (int) $fmatch[4] : $locale['int_frac_digits'];
            $conversion = $fmatch[5];

            $positive = true;
            if ($value < 0) {
                $positive = false;
                $value *= -1;
            }
            $letter = $positive ? 'p' : 'n';

            $prefix = $suffix = $cprefix = $csuffix = $signal = '';

            $signal = $positive ? $locale['positive_sign'] : $locale['negative_sign'];
            switch (true) {
                case $locale["{$letter}_sign_posn"] == 1 && $flags['usesignal'] == '+':
                    $prefix = $signal;
                    break;
                case $locale["{$letter}_sign_posn"] == 2 && $flags['usesignal'] == '+':
                    $suffix = $signal;
                    break;
                case $locale["{$letter}_sign_posn"] == 3 && $flags['usesignal'] == '+':
                    $cprefix = $signal;
                    break;
                case $locale["{$letter}_sign_posn"] == 4 && $flags['usesignal'] == '+':
                    $csuffix = $signal;
                    break;
                case $flags['usesignal'] == '(':
                case $locale["{$letter}_sign_posn"] == 0:
                    $prefix = '(';
                    $suffix = ')';
                    break;
            }
            if (!$flags['nosimbol']) {
                $currency = $cprefix .
                    ($conversion == 'i' ? $locale['int_curr_symbol'] : $locale['currency_symbol']) .
                    $csuffix;
            } else {
                $currency = '';
            }
            $space = $locale["{$letter}_sep_by_space"] ? ' ' : '';

            $value = number_format($value, $right, $locale['mon_decimal_point'],
                $flags['nogroup'] ? '' : $locale['mon_thousands_sep']);
            $value = @explode($locale['mon_decimal_point'], $value);

            $n = strlen($prefix) + strlen($currency) + strlen($value[0]);
            if ($left > 0 && $left > $n) {
                $value[0] = str_repeat($flags['fillchar'], $left - $n) . $value[0];
            }
            $value = implode($locale['mon_decimal_point'], $value);
            if ($locale["{$letter}_cs_precedes"]) {
                $value = $prefix . $currency . $space . $value . $suffix;
            } else {
                $value = $prefix . $value . $space . $currency . $suffix;
            }
            if ($width > 0) {
                $value = str_pad($value, $width, $flags['fillchar'], $flags['isleft'] ?
                    STR_PAD_RIGHT : STR_PAD_LEFT);
            }

            $format = str_replace($fmatch[0], $value, $format);
        }

        return $format;
    }
}

if (!function_exists("json_decode")) {

    function json_decode(
        $json,
        $assoc = false, /* emu_args */
        $n = 0,
        $state = 0,
        $waitfor = 0
    )
    {

        #-- result var
        $val = null;
        static $lang_eq = array("true" => true, "false" => false, "null" => null);
        static $str_eq = array(
            "n" => "\012",
            "r" => "\015",
            "\\" => "\\",
            '"' => '"',
            "f" => "\f",
            "b" => "\b",
            "t" => "\t",
            "/" => "/",
        );

        #-- flat char-wise parsing
        for (/* n */; $n < strlen($json); /* n */) {
            $c = $json[$n];

            #-= in-string
            if ($state === '"') {

                if ($c == '\\') {
                    $c = $json[++$n];
                    // simple C escapes
                    if (isset($str_eq[$c])) {
                        $val .= $str_eq[$c];
                    } // here we transform \uXXXX Unicode (always 4 nibbles) references to UTF-8
                    elseif ($c == "u") {
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
                        $val .= "\\" . $c;
                    }
                } // end of string
                elseif ($c == '"') {
                    $state = 0;
                } // yeeha! a single character found!!!!1!
                else/* if (ord($c) >= 32) */ { //@COMPAT: specialchars check - but native json doesn't do it?
                    $val .= $c;
                }
            } #-> end of sub-call (array/object)
            elseif ($waitfor && (strpos($waitfor, $c) !== false)) {
                return array($val, $n); // return current value and state
            } #-= in-array
            elseif ($state === ']') {
                list($v, $n) = json_decode($json, 0, $n, 0, ",]");
                $val[] = $v;
                if ($json[$n] == "]") {
                    return array($val, $n);
                }
            } #-= in-object
            elseif ($state === '}') {
                list($i, $n) = json_decode($json, 0, $n, 0, ":"); // this allowed non-string indicies
                list($v, $n) = json_decode($json, 0, $n + 1, 0, ",}");
                $val[$i] = $v;
                if ($json[$n] == "}") {
                    return array($val, $n);
                }
            } #-- looking for next item (0)
            else {

                #-> whitespace
                if (preg_match("/\s/", $c)) {
                    // skip
                } #-> string begin
                elseif ($c == '"') {
                    $state = '"';
                } #-> object
                elseif ($c == "{") {
                    list($val, $n) = json_decode($json, $assoc, $n + 1, '}', "}");
                    if ($val && $n && !$assoc) {
                        $obj = new stdClass();
                        foreach ($val as $i => $v) {
                            $obj->{$i} = $v;
                        }
                        $val = $obj;
                        unset($obj);
                    }
                } #-> array
                elseif ($c == "[") {
                    list($val, $n) = json_decode($json, $assoc, $n + 1, ']', "]");
                } #-> comment
                elseif (($c == "/") && ($json[$n + 1] == "*")) {
                    // just find end, skip over
                    ($n = strpos($json, "*/", $n + 1)) or ($n = strlen($json));
                } #-> numbers
                elseif (preg_match("#^(-?\d+(?:\.\d+)?)(?:[eE]([-+]?\d+))?#", substr($json, $n), $uu)) {
                    $val = $uu[1];
                    $n += strlen($uu[0]) - 1;
                    if (strpos($val, ".")) {  // float
                        $val = (float) $val;
                    } elseif ($val[0] == "0") {  // oct
                        $val = octdec($val);
                    } else {
                        $val = (int) $val;
                    }
                    // exponent?
                    if (isset($uu[2])) {
                        $val *= pow(10, (int) $uu[2]);
                    }
                } #-> boolean or null
                elseif (preg_match("#^(true|false|null)\b#", substr($json, $n), $uu)) {
                    $val = $lang_eq[$uu[1]];
                    $n += strlen($uu[1]) - 1;
                } #-- parsing error
                else {
                    // PHPs native json_decode() breaks here usually and QUIETLY
                    trigger_error("json_decode: error parsing '$c' at position $n", E_USER_WARNING);

                    return $waitfor ? array(null, 1 << 30) : null;
                }
            }//state
            #-- next char
            if ($n === null) {
                return null;
            }
            $n++;
        }//for
        #-- final result
        return ($val);
    }

}

function _htmlDistance($distance)
{
    $integer = intval($distance);
    $decimalPosition = strrpos($distance, '.');
    $decimal = $decimalPosition === false ? false : substr($distance, $decimalPosition);

    return intval($distance) . ($decimal ? '<small>' . $decimal . '</small>' : '');
}
