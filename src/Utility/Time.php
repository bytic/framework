<?php

namespace Nip\Utility;

class Time
{

    protected $_value = null;
    protected $_parts = null;
    protected $_seconds = null;

    public function getSeconds()
    {
        if ($this->_seconds === null) {
            $this->parseSeconds();
        }
        return $this->_seconds;
    }

    public function parseSeconds()
    {
        $parts = $this->getParts();
        if (count($parts) == 3) {
            $this->_seconds = 0;
            $this->_seconds += $parts['h'] * 3600;
            $this->_seconds += $parts['m'] * 60;
            $this->_seconds += $parts['s'];
        }
    }

    public function getPart($p)
    {
        if ($this->_parts === null) {
            $this->parseParts();
        }
        return $this->_parts[$p];
    }

    public function getParts()
    {
        if ($this->_parts === null) {
            $this->parseParts();
        }
        return $this->_parts;
    }

    public function parseParts()
    {
        if ($this->_value && substr_count($this->_value, ':') ==2) {
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

        if ($hours = intval((floor($seconds / 3600))) OR $return) {
            $seconds = $seconds - $hours * 3600;
            $return .= ($return ? ' ' : '') . str_pad($hours, 2, 0, STR_PAD_LEFT) . 'h';
        }
        if ($minutes = intval((floor($seconds / 60))) OR $return) {
            $seconds = $seconds - $minutes * 60;
            $return .= ($return ? ' ' : '') . str_pad($minutes, 2, 0, STR_PAD_LEFT) . 'm';
        }

        $seconds = round($seconds, 2);
        if ($seconds) {
            $return .= ($return ? ' ' : '') . str_pad($seconds, 2, 0, STR_PAD_LEFT) . 's';
        }

        return $return;
    }

    public static function fromString($string)
    {
        $o = new self();
        $o->_value = $string;
        $o->parseSeconds();
        return $o;
    }
}
