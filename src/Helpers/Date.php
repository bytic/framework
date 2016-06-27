<?php

class Nip_Helper_Date extends Nip\Helpers\AbstractHelper
{


    public function validate($date, $format)
    {
        $unix = $this->parseUnix($date, $format);
        if ($unix) {
            if (date($format, $unix) == $date) {
                return $unix;
            }
        }
        return false;
    }

    public function calculateAge($date1, $date2 = false)
    {
        $dStart = new DateTime($date1);
        $dEnd = $date2 ? new DateTime($date2) : new DateTime('NOW');

        $dDiff = $dStart->diff($dEnd);

        $years = (int)$dDiff->format("%y");
        $months = (int)$dDiff->format("%m");
        $days = (int)$dDiff->format("%d");

        return array($years, $months, $days);
    }

    public function parseUnix($date, $format)
    {
        return strtotime($date);
    }

    /**
     * Singleton
     *
     * @return Nip_Helper_Date
     */
    static public function instance()
    {
        static $instance;
        if (!($instance instanceof self)) {
            $instance = new self();
        }
        return $instance;
    }

}