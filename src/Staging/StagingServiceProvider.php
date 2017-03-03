<?php

namespace Nip\Staging;

use Nip\Container\ServiceProviders\Providers\AbstractSignatureServiceProvider;

/**
 * Class MailServiceProvider
 * @package Nip\Staging
 */
class StagingServiceProvider extends AbstractSignatureServiceProvider
{

    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->registerStaging();
    }

    protected function registerStaging()
    {
        $staging = new Staging();
        $this->getContainer()->share('staging', $staging);
    }

    /**
     * @inheritdoc
     */
    public function provides()
    {
        return ['staging'];
    }
}
