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
     * @param string $name
     * @return $this
     */
    public function setFileName($name)
    {
        $path_parts = pathinfo($this->getPath());
        $path_parts['filename'] = $name;
        $this->setPath(
            $path_parts['dirname']
            . '/' . $path_parts['filename'] . '.' . $path_parts['extension']
        );

        return $this;
    }

    /**
     * Get File path with init check
     *
     * @return string
     */
    public function getPath()
    {
        if (!$this->path) {
            $this->initPath();
        }
        return parent::getPath();
    }

    /**
     * @return string
     */
    protected function initPath()
    {
        $this->setPath($this->getPathFolder() . $this->getName());
    }

    /**
     * @inheritdoc
     */
    public function setPath($path)
    {
        $name = pathinfo($path, PATHINFO_BASENAME);
        $this->setName($name);
        return parent::setPath($path);
    }

    /**
     * @return string
     */
    public function getPathFolder()
    {
        return '/';
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        if (!$this->name) {
            $this->initName();
        }
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    protected function initName()
    {
        $this->name = $this->getDefaultName();
    }

    /**
     * @return string
     */
    public function getDefaultName()
    {
        return 'file';
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url ? $this->url : $this->getUrlPath() . $this->name;
    }
}
