<?php

namespace Nip\Config;

use Nip\Config\Loaders\FileLoader;

/**
 * Class Factory.
 */
class Factory
{
    /**
     * @param $filename
     * @param bool $returnConfigObject
     * @param bool $useIncludePath
     *
     * @throws Exception\RuntimeException
     *
     * @return mixed|Config
     */
    public static function fromFile($filename, $returnConfigObject = false, $useIncludePath = false)
    {
        $loader = new FileLoader($filename);
        $loader->setUseIncludePath($useIncludePath);
        $loader->setReturnConfigObject($returnConfigObject);

        return $loader->getResult();
    }
}
