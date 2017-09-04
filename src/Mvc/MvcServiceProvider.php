<?php

namespace Nip\Mvc;

use Nip\Container\ServiceProviders\Providers\AbstractSignatureServiceProvider;
use Nip\Mvc\Sections\SectionsManager;

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
        $this->registerSections();
    }

    protected function registerModules()
    {
        $this->getContainer()->share('mvc.modules', function() {
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

    protected function registerSections()
    {
        $this->getContainer()->share('mvc.sections', function() {
            return $this->createSectionsManager();
        });
    }

    /**
     * @return Modules
     */
    protected function createSectionsManager()
    {
        $sections = $this->getContainer()->get(SectionsManager::class);
        $sections->init();
        return $sections;
    }

    /**
     * @inheritdoc
     */
    public function provides()
    {
        return ['mvc.modules', 'mvc.sections'];
    }
}
