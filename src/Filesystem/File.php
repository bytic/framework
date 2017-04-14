<?php

namespace Nip\Filesystem;

/**
 * Class File
 * @package Nip\Filesystem
 */
class File extends \League\Flysystem\File
{

    /**
     * @var
     */
    protected $name;

    /**
     * @var
     */
    protected $url;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
//        $this->path = $this->getPath();
//        $this->url = $this->getUrl();

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url ? $this->url : $this->getUrlPath() . $this->name;
    }
}
