<?php

namespace Nip\AutoLoader\Loaders;

use Nip\AutoLoader\AutoLoader;
use Nip\AutoLoader\Exception as AutoloadException;

/**
 * Class AbstractLoader.
 */
abstract class AbstractLoader
{
    /**
     * @var AutoLoader
     */
    protected $autoloader;

    /**
     * @return AutoLoader
     */
    public function getAutoLoader()
    {
        return $this->autoloader;
    }

    /**
     * @param AutoLoader $autoloader
     */
    public function setAutoLoader($autoloader)
    {
        $this->autoloader = $autoloader;
    }

    /**
     * @param $name
     *
     * @throws AutoloadException
     *
     * @return bool
     */
    public function load($name)
    {
        $file = $this->getClassLocation($name);
        if ($file !== null) {
            if (!is_file($file)) {
                throw new AutoloadException("Invalid filepath [$file] for name [$name");
            }
            /* @noinspection PhpIncludeInspection */
            if (!include($file)) {
                throw new AutoloadException("Cannot include [$name] file [$file]");
            }
            if ($this->isLoaded($name)) {
                return true;
            } else {
                throw new AutoloadException("Cannot find the [$name] class in [$file]");
            }
        }

        return false;
    }

    /**
     * @param $class
     *
     * @return string|bool
     */

    /**
     * @param $class
     *
     * @return string|null
     */
    abstract public function getClassLocation($class);

    /**
     * @param $name
     *
     * @return bool
     */
    public function isLoaded($name)
    {
        return class_exists($name, false) || interface_exists($name, false) || trait_exists($name, false);
    }
}
