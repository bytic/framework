<?php

namespace Nip;

use Nip\AutoLoader\Exception;
use Nip\AutoLoader\Exception as AutoloadException;
use Nip\AutoLoader\Loaders\AbstractLoader;
use Nip\AutoLoader\Loaders\ClassMap;

/**
 * Class AutoLoader
 * @package Nip
 */
class AutoLoader
{

    /**
     * @var bool
     */
    public static $splHandler = false;

    /**
     * @var AbstractLoader[]
     */
    protected $loaders = [];

    /**
     * @var string
     */
    protected $cachePath;

    /**
     * @var array
     */
    protected $ignoreTokens = [];

    /**
     * @param AutoLoader $autoloader
     * @return bool
     * @throws Exception
     */
    public static function registerHandler(AutoLoader $autoloader)
    {
        // Only register once per instance
        if (static::$splHandler) {
            return false;
        }

        if ($autoloader instanceof AutoLoader) {
            return spl_autoload_register([$autoloader, 'autoload']);
        }

        throw new AutoloadException('Invalid Autoloader specified in register handler');
    }

    /**
     * Singleton
     *
     * @return self
     */
    public static function instance()
    {
        static $instance;
        if (!($instance instanceof self)) {
            $instance = new self();
        }

        return $instance;
    }

    /**
     * @param $dir
     * @return $this
     */
    public function addDirectory($dir)
    {
        $this->getClassMapLoader()->addDirectory($dir);

        return $this;
    }

    /**
     * @return ClassMap
     */
    public function getClassMapLoader()
    {
        return $this->getLoader('ClassMap');
    }

    /**
     * @param $name
     * @return AbstractLoader
     */
    public function getLoader($name)
    {
        if (!$this->hasLoader($name)) {
            $this->initLoader($name);
        }

        return $this->loaders[$name];
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasLoader($name)
    {
        return isset($this->loaders[$name]);
    }

    /**
     * @param $name
     */
    public function initLoader($name)
    {
        $loader = $this->newLoader($name);
        $this->addLoader($name, $loader);
    }

    /**
     * @param $name
     * @return AbstractLoader
     */
    public function newLoader($name)
    {
        $class = $this->getLoaderClass($name);
        $loader = new $class;

        return $loader;
    }

    /**
     * @param $name
     * @return string
     */
    public function getLoaderClass($name)
    {
        return 'Nip\AutoLoader\Loaders\\'.$name;
    }

    /**
     * @param $name
     * @param AbstractLoader $loader
     */
    public function addLoader($name, $loader)
    {
        $loader->setAutoloader($this);
        $this->loaders[$name] = $loader;
    }

    /**
     * @return mixed
     */
    public function getCachePath()
    {
        return $this->cachePath;
    }

    /**
     * @param $path
     * @return $this
     */
    public function setCachePath($path)
    {
        $this->cachePath = $path;

        return $this;
    }

    /**
     * @param $class
     * @return bool
     */
    public function isClass($class)
    {
        return is_file($this->getClassLocation($class));
    }

    /**
     * @param $class
     * @return null|string
     */
    public function getClassLocation($class)
    {
        $loaders = $this->getLoaders();
        foreach ($loaders as $loader) {
            $path = $loader->getClassLocation($class);
            if ($path) {
                return $path;
            }
        }

        return null;
    }

    /**
     * @return AutoLoader\Loaders\AbstractLoader[]
     */
    public function getLoaders()
    {
        return $this->loaders;
    }

    /**
     * @param $class
     * @return bool
     */
    public function autoload($class)
    {
        try {
            return $this->load($class);
        } catch (AutoloadException $ex) {
        }

        return false;
    }

    /**
     * @param $class
     * @return bool
     */
    public function load($class)
    {
        if ($this->hasIgnoreTokens($class)) {
            return true;
        }

        if (class_exists($class, false)) {
            return true;
        }

        $loaders = $this->getLoaders();
        foreach ($loaders as $loader) {
            if ($loader->load($class)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $token
     * @return bool
     */
    public function hasIgnoreTokens($token)
    {
        return in_array($token, $this->ignoreTokens);
    }

    /**
     * @param $token
     */
    public function addIgnoreTokens($token)
    {
        $this->ignoreTokens[] = $token;
    }

    /**
     * @return array
     */
    public function getIgnoreTokens()
    {
        return $this->ignoreTokens;
    }

    /**
     * @param array $ignoreTokens
     */
    public function setIgnoreTokens($ignoreTokens)
    {
        $this->ignoreTokens = $ignoreTokens;
    }
}
