<?php

namespace Nip\Filesystem;

use League\Flysystem\FilesystemInterface;

/**
 * Class File
 * @package Nip\Filesystem
 *
 * @method FilesystemInterface|FileDisk getFilesystem
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
     * @inheritdoc
     */
    public function __construct(FilesystemInterface $filesystem = null, $path = null)
    {
        $this->parseNameFromPath($path);
        return parent::__construct($filesystem, $path);
    }

    /**
     * @param $path
     */
    protected function parseNameFromPath($path)
    {
        $name = pathinfo($path, PATHINFO_BASENAME);
        $this->setName($name);
    }

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
     * @return void
     */
    protected function initPath()
    {
        $this->setPath($this->getPathFolder() . $this->getName());
    }

    /**
     * @inheritdoc
     * @param string $path
     */
    public function setPath($path)
    {
        $this->parseNameFromPath($path);
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
     * @return mixed
     */
    public function getUrl()
    {
        if (!$this->url) {
            $this->initUrl();
        }
        return $this->url;
    }

    protected function initUrl()
    {
        $this->url = $this->getFilesystem()->getUrl($this->getPath());
    }
}
