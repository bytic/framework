<?php

namespace Nip\Mvc\Sections;

/**
 * Class Section
 * @package Nip\Mvc\Sections
 */
class Section
{

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $subdomain;

    /**
     * Section constructor.
     * @param array $data
     */
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getSubdomain()
    {
        return $this->subdomain;
    }

    public function getURL($url = false)
    {
        $url = $url ? $url : BASE_URL;
        return str_replace('://' . \Nip\Request::instance()->getHttp()->getSubdomain() . '.42km', '://' . $this->subdomain . '.42km', $url);
    }

    /**
     * @param bool $path
     * @return bool|mixed
     */
    public function getPath($path = false)
    {
        $curentSubdomain = request()->getHttp()->getSubdomain();
        if (request()->getHttp()->getSubdomain() == 'www') {
            $path = str_replace(ROOT_PATH, ROOT_PATH . $this->subdomain . DS, $path);
        } elseif ($this->subdomain == 'www') {
            $path = str_replace($curentSubdomain . DS, '', $path);
        }
        return $path;
    }

    /**
     * @return bool
     */
    public function isCurrent()
    {
        return $this->subdomain == request()->getHttp()->getSubdomain();
    }

    /**
     * @return bool
     */
    public function isMenu()
    {
        return $this->menu === true;
    }
}
