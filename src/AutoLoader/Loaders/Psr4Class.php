<?php

namespace Nip\AutoLoader\Loaders;

/**
 * Class Psr4Class
 * @package Nip\AutoLoader\Loaders
 */
class Psr4Class extends AbstractLoader
{
    /**
     * @var array
     */
    private $prefixes = [];

    /**
     * @param string $prefix
     * @param string $baseDir
     */
    public function addPrefix($prefix, $baseDir)
    {
        $prefix = trim($prefix, '\\').'\\';
        $baseDir = rtrim($baseDir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
        $this->prefixes[] = [$prefix, $baseDir];
    }

    /**
     * @param string $class
     * @return bool
     */
    public function getClassLocation($class)
    {
        return $this->findFile($class);
    }

    /**
     * @param string $class
     *
     * @return string|null
     */
    public function findFile($class)
    {
        $class = ltrim($class, '\\');
        foreach ($this->prefixes as list($currentPrefix, $currentBaseDir)) {
            if (0 === strpos($class, $currentPrefix)) {
                $classWithoutPrefix = substr($class, strlen($currentPrefix));
                $file = $currentBaseDir.str_replace('\\', DIRECTORY_SEPARATOR, $classWithoutPrefix).'.php';
                if (file_exists($file)) {
                    return $file;
                }
            }
        }

        return null;
    }
}
