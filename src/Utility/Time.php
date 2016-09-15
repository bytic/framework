<?php

namespace Nip\Utility;

class Time
{

    protected $_value = null;
    protected $_parts = null;
    protected $_seconds = null;

    public static function fromString($string)
    {
        $o = new self();
        $o->_value = $string;
        $o->parseSeconds();

        return $o;
    }

    public static function fromSeconds($seconds)
    {
        $o = new self();
        $o->setSeconds($seconds);

        return $o;
    }

    public function getPart($p)
    {
        if ($this->_parts === null) {
            $this->parseParts();
        }

        return $this->_parts[$p];
    }

    public function parseParts()
    {
        if ($this->_value && substr_count($this->_value, ':') == 2) {
            list ($h, $m, $s) = explode(':', $this->_value);
            $this->_parts = array(
                'h' => $h,
                'm' => $m,
                's' => $s,
            );
        }
    }

    public function getFormatedString()
    {
        $seconds = $this->getSeconds();
        $return = '';

        if ($hours = intval((floor($seconds / 3600))) OR $return) {
            $seconds = $seconds - $hours * 3600;
            $return .= ($return ? ' ' : '').str_pad($hours, 2, 0, STR_PAD_LEFT).'h';
        }
        if ($minutes = intval((floor($seconds / 60))) OR $return) {
            $seconds = $seconds - $minutes * 60;
            $return .= ($return ? ' ' : '').str_pad($minutes, 2, 0, STR_PAD_LEFT).'m';
        }

        $seconds = round($seconds, 2);
        if ($seconds) {
            $return .= ($return ? ' ' : '').str_pad($seconds, 2, 0, STR_PAD_LEFT).'s';
        }

        return $return;
    }

    public function getSeconds()
    {
        if ($this->_seconds === null) {
            $this->parseSeconds();
        }

        return $this->_seconds;
    }

    /**
     * @param null $seconds
     */
    public function setSeconds($seconds)
    {
        $this->_seconds = $seconds;
    }

    public function parseSeconds()
    {
        $parts = $this->getParts();
        if (count($parts) == 3) {
            $seconds = 0;
            $seconds += $parts['h'] * 3600;
            $seconds += $parts['m'] * 60;
            $seconds += $parts['s'];
            $this->setSeconds($seconds);
        }
    }

    public function getParts()
    {
        if ($this->_parts === null) {
            $this->parseParts();
        }

        return $this->_parts;
    }
}