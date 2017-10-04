<?php

class Nip_Helper_Hash extends Nip\Helpers\AbstractHelper
{
    public function uhash($num, $len = 10, $base = 36)
    {
        $golden_primes = array(
            36 => array(1, 23, 809, 28837, 1038073, 37370257, 1345328833)
        );
        $gp = $golden_primes[$base];
        $maxlen = count($gp);
        $len = $len > ($maxlen - 1) ? ($maxlen - 1) : $len;
        while ($len < $maxlen && pow($base, $len) < $num) {
            $len++;
        }
        if ($len >= $maxlen) {
            throw new Exception($num . " out of range (max " . pow($base, $maxlen - 1) . ")");
        }
        $ceil = pow($base, $len);
        $prime = $gp[$len];
        $dechash = ($num * $prime) % $ceil;
        $hash = base_convert($dechash, 10, $base);
        return str_pad($hash, $len, "0", STR_PAD_LEFT);
    }

    /**
     * Singleton
     *
     * @return Nip_Helper_Hash
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
