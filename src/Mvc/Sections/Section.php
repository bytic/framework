<?php

namespace Nip\Mvc\Sections;

use Nip\Utility\Traits\DynamicPropertiesTrait;

/**
 * Class Section
 * @package Nip\Mvc\Sections
 */
class Section
{
    use DynamicPropertiesTrait;

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

    /**
     * @param bool $url
     * @return mixed
     */
    public function getURL($url = false)
    {
        $url = $url ? $url : \Nip\url()->to('/');
        $http = request()->getHttp();
        return str_replace(
            '://' . $http->getSubdomain() . '.' . $http->getRootDomain(),
            '://' . $this->subdomain . '.' . $http->getRootDomain(),
            $url
        );
    }

    /**
     * @param bool $path
     * @return bool|mixed
     */
    public function getPath($path = false)
    {
        $curentSubdomain = request()->getHttp()->getSubdomain();
        if (request()->getHttp()->getSubdomain() == 'www') {
            $path = str_replace(ROOT_PATH, ROOT_PATH . $this->subdomain . DIRECTORY_SEPARATOR, $path);
        } elseif ($this->subdomain == 'www') {
            $path = str_replace($curentSubdomain . DIRECTORY_SEPARATOR, '', $path);
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
