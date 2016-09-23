<?php

namespace Nip\AutoLoader\Loaders;

use Nip\AutoLoader;
use Nip\AutoLoader\Exception as AutoloadException;

/**
 * Class AbstractLoader
 * @package Nip\AutoLoader\Loaders
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
    public function getAutoloader()
    {
        return $this->autoloader;
    }

    /**
     * @param AutoLoader $autoloader
     */
    public function setAutoloader($autoloader)
    {
        $this->autoloader = $autoloader;
    }

    /**
     * @param $name
     * @return bool
     * @throws AutoloadException
     */
    public function load($name)
    {
        $file = $this->getClassLocation($name);
        if ($file !== null) {
            /** @noinspection PhpIncludeInspection */
            if (include($file)) {
                if ($this->isLoaded($name)) {
                    return true;
                } else {
                    throw new AutoloadException("Cannot find the $name class in $file");
                }
            } else {
                throw new AutoloadException("Cannot include $name file $file");
            }
        }

        return false;
    }

    /**
     * @param $class
     * @return string|boolean
     */

    /**
     * @param $class
     * @return string|null
     */
    abstract public function getClassLocation($class);

    /**
     * @param $name
     * @return bool
     */
    public function isLoaded($name)
    {
        return class_exists($name, false) || interface_exists($name, false) || trait_exists($name, false);
    }
}
