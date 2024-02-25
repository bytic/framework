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
    return defined('CURRENT_URL') ? CURRENT_URL : url()->full();
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
