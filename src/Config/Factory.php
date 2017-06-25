<?php

namespace Nip\Config;

use Nip\Config\Loaders\FileLoader;

/**
 * Class Factory
 * @package Nip\Config
 */
class Factory
{
    /**
     * @param Config $repository
     * @param $files
     */
    public static function fromFiles(Config $repository, $files)
    {
        foreach ($files as $key => $path) {
            $data = self::fromFile($path, false);
            $repository->set($key, $data);
        }
    }

    /**
     * @param $filename
     * @param bool $returnConfigObject
     * @param bool $useIncludePath
     * @return mixed|Config
     * @throws Exception\RuntimeException
     */
    public static function fromFile($filename, $returnConfigObject = false, $useIncludePath = false)
    {
        $loader = new FileLoader($filename);
        $loader->setUseIncludePath($useIncludePath);
        $loader->setReturnConfigObject($returnConfigObject);

        return $loader->getResult();
    }
}
