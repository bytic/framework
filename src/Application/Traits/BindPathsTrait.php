<?php

namespace Nip\Application\Traits;

/**
 * Class BindPathsTrait
 * @package Nip\Application\Traits
 */
trait BindPathsTrait
{

    /**
     * The base path for the Laravel installation.
     *
     * @var string
     */
    protected $basePath;

    /**
     * The custom database path defined by the developer.
     *
     * @var string
     */
    protected $databasePath;

    /**
     * The custom storage path defined by the developer.
     *
     * @var string
     */
    protected $storagePath;

    /**
     * The custom environment path defined by the developer.
     *
     * @var string
     */
    protected $environmentPath;

    /**
     * Set the base path for the application.
     *
     * @param  string $basePath
     * @return $this
     */
    public function setBasePath($basePath)
    {
        $this->basePath = rtrim($basePath, '\/');
        return $this;
    }

    /**
     * Bind all of the application paths in the container.
     *
     * @return void
     */
    public function bindPathsInContainer()
    {
        $this->share('path', $this->path());
        $this->share('path.base', $this->basePath());
        $this->share('path.lang', $this->langPath());
        $this->share('path.config', $this->configPath());
        $this->share('path.public', $this->publicPath());
        $this->share('path.storage', $this->storagePath());
        $this->share('path.database', $this->databasePath());
        $this->share('path.resources', $this->resourcePath());
        $this->share('path.bootstrap', $this->bootstrapPath());
    }

    /**
     * Get the path to the application "app" directory.
     *
     * @return string
     */
    public function path()
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'application';
    }

    /**
     * Get the base path of the Laravel installation.
     *
     * @return string
     */
    public function basePath()
    {
        return $this->basePath;
    }

    /**
     * Get the path to the language files.
     *
     * @return string
     */
    public function langPath()
    {
        return $this->resourcePath() . DIRECTORY_SEPARATOR . 'lang';
    }

    /**
     * Get the path to the resources directory.
     *
     * @return string
     */
    public function resourcePath()
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'resources';
    }

    /**
     * Get the path to the application configuration files.
     *
     * @return string
     */
    public function configPath()
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'config';
    }

    /**
     * Get the path to the public / web directory.
     *
     * @return string
     */
    public function publicPath()
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'public';
    }

    /**
     * Get the path to the storage directory.
     *
     * @return string
     */
    public function storagePath()
    {
        return $this->storagePath ?: $this->basePath . DIRECTORY_SEPARATOR . 'storage';
    }

    /**
     * Get the path to the database directory.
     *
     * @return string
     */
    public function databasePath()
    {
        return $this->databasePath ?: $this->basePath . DIRECTORY_SEPARATOR . 'database';
    }

    /**
     * Get the path to the bootstrap directory.
     *
     * @return string
     */
    public function bootstrapPath()
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'bootstrap';
    }
}
