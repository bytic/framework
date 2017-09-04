<?php

/**
 * Class Nip_Helper_Passwords
 */
class Nip_Helper_Passwords extends Nip\Helpers\AbstractHelper
{

    /**
     * @var string
     */
    protected $salt = null;


    /**
     * @param $password
     * @return string
     */
    public function hash($password)
    {
        return md5($this->getSalt() . $password);
    }

    /**
     * @return string
     */
    public function getSalt()
    {
        if ($this->salt === null) {
            $this->initSalt();
        }

        return $this->salt;
    }

    /**
     * @param $salt
     * @return $this
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    protected function initSalt()
    {
        $salt = '';
        if (app()->has('config')) {
            $config = app()->get('config');
            if ($config->has('PASSWORD.salt')) {
                $salt = $config->get('PASSWORD.salt');
            }
        }
        $this->setSalt($salt);
    }

    /**
     * @param int $length
     * @param bool $use_upper
     * @param bool $use_lower
     * @param bool $use_number
     * @param string $use_custom
     * @return string
     */
    public function generate($length = 8, $use_upper = true, $use_lower = true, $use_number = true, $use_custom = "")
    {
        $lower = implode('', range('a', 'z'));
        $upper = strtoupper($lower);
        $numbers = implode('', range(0, 9));

        $seed_length = 0;
        $seed = '';
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
            $seed_length += strlen($use_custom);
            $seed .= $use_custom;
        }

        $password = '';
        for ($x = 1; $x <= $length; $x++) {
            $password .= $seed{rand(0, $seed_length - 1)};
        }

        return $password;
    }
}
