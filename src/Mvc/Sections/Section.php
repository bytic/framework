<?php

namespace Nip\Mvc\Sections;

use Nip\Utility\Traits\DynamicPropertiesTrait;

/**
 * Class Section
 * @package Nip\Mvc\Sections
 *
 * @property $menu
 * @property $folder
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
     * @var string
     */
    protected $path = null;

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
     * Compile path for this section from a given path of current section
     *
     * @param bool $path
     * @return bool|mixed
     */
    public function compilePath($path = false)
    {
        $currentBasePath = $this->getManager()->getCurrent()->getPath();
        $path = str_replace($currentBasePath, $this->getPath(), $path);
        return $path;
    }

    /**
     * Return the path for section
     *
     * @return null|string
     */
    public function getPath()
    {
        if ($this->path === null) {
            $this->initPath();
        }
        return $this->path;
    }

    protected function initPath()
    {
        $this->path = $this->generatePath();
    }

    /**
     * @return string
     */
    protected function generatePath()
    {
        $path = app('path.base');
        if (!$this->isCurrent()) {
            $path = str_replace(
                DIRECTORY_SEPARATOR . $this->getManager()->getCurrent()->getFolder() . '',
                DIRECTORY_SEPARATOR . $this->getFolder() . '',
                $path
            );
        }
        return $path;
    }

    /**
     * @return bool
     */
    public function isCurrent()
    {
        return $this->getName() == $this->getManager()->getCurrent()->getName();
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return SectionsManager
     */
    protected function getManager()
    {
        return app('mvc.sections');
    }

    /**
     * @return string
     */
    public function getFolder()
    {
        return $this->folder;
    }

    /**
     * @return bool
     */
    public function isMenu()
    {
        return $this->menu === true;
    }
}
