<?php

namespace Nip\Inflector;

use Nip\Container\ServiceProviders\Providers\AbstractSignatureServiceProvider;

/**
 * Class DebugBarServiceProvider
 * @package Nip\DebugBar
 */
class InflectorServiceProvider extends AbstractSignatureServiceProvider
{
    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->getContainer()->share('inflector', function () {
            $inflector = new Inflector();
            $path = app('path.storage') . DIRECTORY_SEPARATOR . 'cache';
            $inflector->setCachePath($path);
            return $inflector;
        });
    }

    /**
     * @inheritdoc
     */
    public function provides()
    {
        return ['inflector'];
    }
}
