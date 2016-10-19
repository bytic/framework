<?php

namespace Nip\Config\Loaders;

use Nip\Config\Config;

/**
 * Class AbstractLoader
 * @package Nip\Config\Loaders
 */
abstract class AbstractLoader
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $resolvedPath = null;

    /**
     * @var bool
     */
    protected $useIncludePath = false;

    /**
     * @var bool
     */
    protected $returnConfigObject = false;

    /**
     * Constructor.
     *
     * @param string $path A path where to look for resources
     */
    public function __construct($path)
    {
        $this->setPath($path);
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getResolvedPath()
    {
        if ($this->resolvedPath === null) {
            $this->setResolvedPath($this->resolvePath());
        }

        return $this->resolvedPath;
    }

    /**
     * @param string $resolvedPath
     */
    public function setResolvedPath($resolvedPath)
    {
        $this->resolvedPath = $resolvedPath;
    }

    /**
     * @return string
     */
    abstract protected function resolvePath();

    /**
     * @return string
     */
    public function getResult()
    {
        $data = $this->getData();

        return ($this->returnConfigObject()) ? new Config($data) : $data;
    }

    /**
     * @return string
     */
    abstract protected function getData();

    /**
     * @return boolean
     */
    public function returnConfigObject()
    {
        return $this->returnConfigObject;
    }

    /**
     * @param boolean $useIncludePath
     */
    public function setUseIncludePath($useIncludePath)
    {
        $this->useIncludePath = $useIncludePath;
    }

    /**
     * @return boolean
     */
    public function useIncludePath()
    {
        return $this->useIncludePath;
    }

    /**
     * @param boolean $returnConfigObject
     */
    public function setReturnConfigObject($returnConfigObject)
    {
        $this->returnConfigObject = $returnConfigObject;
    }
}
