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