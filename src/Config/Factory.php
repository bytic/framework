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
