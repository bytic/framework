<?php

namespace Nip\Staging;

use Nip\Container\ServiceProvider\AbstractSignatureServiceProvider;

/**
 * Class MailServiceProvider.
 */
class StagingServiceProvider extends AbstractSignatureServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->registerStaging();
    }

    protected function registerStaging()
    {
        $staging = new Staging();
        $this->getContainer()->singleton('staging', $staging);
    }

    /**
     * {@inheritdoc}
     */
    public function provides()
    {
        return ['staging'];
    }
}
