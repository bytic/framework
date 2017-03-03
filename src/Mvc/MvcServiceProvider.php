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
        $modules = new Modules();
        $this->getContainer()->share('mvc.modules', $modules);
    }

    /**
     * @inheritdoc
     */
    public function provides()
    {
        return ['mvc.modules'];
    }
}
