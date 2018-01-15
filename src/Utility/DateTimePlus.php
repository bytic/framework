<?php

/*
 * Based on briannesbitt/Carbon
 * http://carbon.nesbot.com/
 */

namespace Nip\Utility;

use DateTime;
use DateTimeZone;
use InvalidArgumentException;

class DateTimePlus extends DateTime
{
    ///////////////////////////////////////////////////////////////////
    ///////////////////////// GETTERS AND SETTERS /////////////////////
    ///////////////////////////////////////////////////////////////////

    /**
     * Get a part of the Carbon object.
     *
     * @param string $name
     *
     * @throws InvalidArgumentException
     *
     * @return string|int|\DateTimeZone
     */
    public function __get($name)
    {
        switch (true) {
            case array_key_exists($name, $formats = [
                'year'        => 'Y',
                'yearIso'     => 'o',
                'month'       => 'n',
                'day'         => 'j',
                'hour'        => 'G',
                'minute'      => 'i',
                'second'      => 's',
                'micro'       => 'u',
                'dayOfWeek'   => 'w',
                'dayOfYear'   => 'z',
                'weekOfYear'  => 'W',
                'daysInMonth' => 't',
                'timestamp'   => 'U',
            ]):
                return (int) $this->format($formats[$name]);
            case $name === 'weekOfMonth':
                return (int) ceil($this->day / static::DAYS_PER_WEEK);
            case $name === 'age':
                return (int) $this->diffInYears();
            case $name === 'quarter':
                return (int) ceil($this->month / 3);
            case $name === 'offset':
                return $this->getOffset();
            case $name === 'offsetHours':
                return $this->getOffset() / static::SECONDS_PER_MINUTE / static::MINUTES_PER_HOUR;
            case $name === 'dst':
                return $this->format('I') === '1';
            case $name === 'local':
                return $this->offset === $this->copy()->setTimezone(date_default_timezone_get())->offset;
            case $name === 'utc':
                return $this->offset === 0;
            case $name === 'timezone' || $name === 'tz':
                return $this->getTimezone();
            case $name === 'timezoneName' || $name === 'tzName':
                return $this->getTimezone()->getName();
            default:
                throw new InvalidArgumentException(sprintf("Unknown getter '%s'", $name));
        }
    }

    /**
     * Check if an attribute exists on the object.
     *
     * @param string $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        try {
            $this->__get($name);
        } catch (InvalidArgumentException $e) {
            return false;
        }

        return true;
    }

    /**
     * Set a part of the Date object.
     *
     * @param string                   $name
     * @param string|int|\DateTimeZone $value
     *
     * @throws InvalidArgumentException
     */
    public function __set($name, $value)
    {
        switch ($name) {
            case 'year':
                $this->setDate($value, $this->month, $this->day);
                break;
            case 'month':
                $this->setDate($this->year, $value, $this->day);
                break;
            case 'day':
                $this->setDate($this->year, $this->month, $value);
                break;
            case 'hour':
                $this->setTime($value, $this->minute, $this->second);
                break;
            case 'minute':
                $this->setTime($this->hour, $value, $this->second);
                break;
            case 'second':
                $this->setTime($this->hour, $this->minute, $value);
                break;
            case 'timestamp':
                parent::setTimestamp($value);
                break;
            case 'timezone':
            case 'tz':
                $this->setTimezone($value);
                break;
            default:
                throw new InvalidArgumentException(sprintf("Unknown setter '%s'", $name));
        }
    }

    /**
     * Set the instance's year.
     *
     * @param int $value
     *
     * @return static
     */
    public function year($value)
    {
        $this->year = $value;

        return $this;
    }

    /**
     * Set the instance's month.
     *
     * @param int $value
     *
     * @return static
     */
    public function month($value)
    {
        $this->month = $value;

        return $this;
    }

    /**
     * Set the instance's day.
     *
     * @param int $value
     *
     * @return static
     */
    public function day($value)
    {
        $this->day = $value;

        return $this;
    }

    /**
     * Set the instance's hour.
     *
     * @param int $value
     *
     * @return static
     */
    public function hour($value)
    {
        $this->hour = $value;

        return $this;
    }

    /**
     * Set the instance's minute.
     *
     * @param int $value
     *
     * @return static
     */
    public function minute($value)
    {
        $this->minute = $value;

        return $this;
    }

    /**
     * Set the instance's second.
     *
     * @param int $value
     *
     * @return static
     */
    public function second($value)
    {
        $this->second = $value;

        return $this;
    }

    public static function createFromFormat($format, $time, DateTimeZone $timezone = null)
    {
        if ($timezone !== null) {
            $dt = parent::createFromFormat($format, $time, static::safeCreateDateTimeZone($timezone));
        } else {
            $dt = parent::createFromFormat($format, $time);
        }

        if ($dt instanceof DateTime) {
            return static::instance($dt);
        }
        $errors = static::getLastErrors();

        throw new InvalidArgumentException(implode(PHP_EOL, $errors['errors']));
    }

    /**
     * Modify date to this year.
     *
     * @return static
     */
    public function currentYear()
    {
        return $this->year(date('Y'));
    }

    /**
     * Create a DatePlus instance from a DateTime one.
     *
     * @param \DateTime $dt
     *
     * @return static
     */
    public static function instance(DateTime $dt)
    {
        if ($dt instanceof static) {
            return clone $dt;
        }

        return new static($dt->format('Y-m-d H:i:s.u'), $dt->getTimeZone());
    }
}
