<?php

namespace Nip\Locale;

use Locale as PhpLocale;
use Nip_File_System;

/**
 * Class Locale
 * @package Nip\Locale
 */
class Locale
{
    protected $supported;

    protected $data = [];

    protected $default = 'en_US';

    protected $current;

    public function getSupported()
    {
        if (!$this->supported) {
            $files = Nip_File_System::instance()->scanDirectory($this->getDataFolder());
            foreach ($files as $file) {
                if (substr($file, 0, 1) != '_') {
                    $name = str_replace('.php', '', $file);
                    $this->supported[] = $name;
                }
            }
        }
        return $this->supported;
    }

    /**
     * @return string
     */
    protected function getDataFolder()
    {
        return dirname(__FILE__) . '/data/';
    }

    /**
     * @param string[] $path
     * @param bool $locale
     * @return bool|mixed
     */
    public function getOption($path = [], $locale = false)
    {
        $data = $this->getData($locale);
        $value = $data;
        $pathFlat = '';
        foreach ($path as $key) {
            $pathFlat .= $key;
            if (isset($value[$key])) {
                $value = $value[$key];
            } else {
                trigger_error("invalid path [{$pathFlat}] for " . __CLASS__ . "->" . __METHOD__, E_USER_WARNING);
                return false;
            }
        }

        return $value;
    }

    /**
     * @param bool $locale
     * @return mixed
     */
    public function getData($locale = false)
    {
        $locale = $locale ? $locale : $this->getCurrent();
        if (!isset($this->data[$locale])) {
            $data = $this->getDataFromFile($locale);
            $this->data[$locale] = $data;
        }

        return $this->data[$locale];
    }

    public function getCurrent()
    {
        if (!$this->current) {
            $this->initCurrent();
        }

        return $this->current;
    }

    /**
     * @param string $locale
     */
    public function setCurrent($locale)
    {
        if ($this->isSupported($locale)) {
            $this->current = $locale;
        } else {
            $this->current = $this->default;
        }
    }

    public function initCurrent()
    {
        $locale = $this->getFromINI();
        if ($this->isSupported($locale)) {
            $this->setCurrent($locale);
        } else {
            $this->setCurrent($this->default);
        }
    }

    /**
     * @return string
     */
    public function getFromINI()
    {
        if (class_exists('Locale', false)) {
            return PhpLocale::getDefault();
        }

        return setlocale(LC_TIME, 0);
    }

    /**
     * @param $name
     * @return bool
     */
    public function isSupported($name)
    {
        return $this->hasDataFile($name);
    }

    /**
     * @param $name
     * @return bool
     */
    protected function hasDataFile($name)
    {
        return is_file($this->getDataFile($name));
    }

    /**
     * @param $name
     * @return string
     */
    protected function getDataFile($name)
    {
        return $this->getDataFolder() . $name . '.php';
    }

    /**
     * @param $name
     * @param array $data
     * @return array
     */
    protected function getDataFromFile($name, $data = [])
    {
        $file = $this->getDataFile($name);

        if (is_file($file)) {
            include $file;
            if (isset($_import)) {
                $data = $this->getDataFromFile($_import);
            }
            if (isset($_data)) {
                $data = \Nip\HelperBroker::get('Arrays')->merge_distinct($data, $_data);
            }
        } else {
            trigger_error("no locale data file at [{$file}]", E_USER_NOTICE);
        }

        return $data;
    }
}
