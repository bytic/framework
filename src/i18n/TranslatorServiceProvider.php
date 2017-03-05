<?php

namespace Nip\I18n;

use Nip\Container\ServiceProviders\Providers\AbstractSignatureServiceProvider;

/**
 * Class MailServiceProvider
 * @package Nip\Mail
 */
class TranslatorServiceProvider extends AbstractSignatureServiceProvider
{
    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->registerTranslator();
    }

    /**
     * Register the session manager instance.
     *
     * @return void
     */
    protected function registerTranslator()
    {
        $this->getContainer()->share('translator', Translator::class);
    }

    /**
     * @inheritdoc
     */
    public function provides()
    {
        return ['translator'];
    }
}