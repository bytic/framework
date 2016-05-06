<?php
class Nip_Helper_Passwords extends Nip_Helper {

    protected $salt;


    public function __construct() {
        $this->salt = Nip_Config::instance()->PASSWORD->salt;
    }


    public function hash($password) {
        return md5($this->salt . $password);
    }


    public function generate($length = 8, $use_upper = true, $use_lower = true, $use_number = true, $use_custom = "") {
        $lower		= implode('', range('a', 'z'));
        $upper		= strtoupper($lower);
        $numbers	= implode('', range(0, 9));

        if ($use_upper) {
            $seed_length += 26;
            $seed .= $upper;
        }

        if ($use_lower) {
            $seed_length += 26;
            $seed .= $lower;
        }

        if ($use_number) {
            $seed_length += 10;
            $seed .= $numbers;
        }

        if ($use_custom) {
            $seed_length +=strlen($use_custom);
            $seed .= $use_custom;
        }

        for ($x = 1; $x <= $length; $x++) {
            $password .= $seed{rand(0, $seed_length - 1)};
        }

        return $password;
    }


    public function setSalt($salt) {
        $this->salt = $salt;
        return $this;
    }


    /**
     * Singleton
     *
     * @return Nip_Helper_Passwords
     */
    static public function instance() {
        static $instance;
        if (!($instance instanceof self)) {
            $instance = new self();
        }
        return $instance;
    }

}