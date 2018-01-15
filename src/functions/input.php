<?php

function fix_input_quotes()
{
    if (get_magic_quotes_gpc()) {
        array_stripslashes($_GET);
        array_stripslashes($_POST);
        array_stripslashes($_COOKIE);
    }
}

function array_stripslashes(&$array)
{
    if (!is_array($array)) {
        return;
    }
    foreach ($array as $k => $v) {
        if (is_array($array[$k])) {
            array_stripslashes($array[$k]);
        } else {
            $array[$k] = stripslashes($array[$k]);
        }
    }

    return $array;
}

function clean($input)
{
    return trim(stripslashes(htmlentities($input, ENT_QUOTES, 'UTF-8')));
}

function strToASCII($str)
{
    $trans = [
    'Š'=> 'S', 'Ș'=>'S', 'š'=>'s', 'ș'=>'s', 'Ð'=>'Dj', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Ă' => 'A',
    'Å'=> 'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I',
    'Ï'=> 'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Ț' => 'T',
    'Û'=> 'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'ă' => 'a',
    'å'=> 'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i',
    'ï'=> 'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u',
    'ú'=> 'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'ƒ'=>'f', 'ț' => 't', ];

    return strtr($str, $trans);
}
