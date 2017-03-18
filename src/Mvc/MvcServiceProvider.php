<?php

namespace Nip\Mvc;

use Nip\Container\ServiceProviders\Providers\AbstractSignatureServiceProvider;

/**
 * Class MailServiceProvider
 * @package Nip\Mail
 */
class MvcServiceProvider extends AbstractSignatureServiceProvider
{

    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->registerModules();
    }

    protected function registerModules()
    {
        $this->getContainer()->share('mvc.modules', function () {
            return $this->createModulesProvider();
        });
    }

    /**
     * @return Modules
     */
    protected function createModulesProvider()
    {
        $modules = $this->getContainer()->get(Modules::class);
        return $modules;
    }

    /**
     * @inheritdoc
     */
    public function provides()
    {
        return ['mvc.modules'];
    }
}
