<?php

namespace Nip\I18n;

use Nip\Container\ServiceProviders\Providers\AbstractSignatureServiceProvider;
use Nip\I18n\Translator\Backend\AbstractBackend;
use Nip\I18n\Translator\Backend\File;

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
        $this->registerLanguages();
        $this->registerLoader();
    }

    /**
     * Register the session manager instance.
     *
     * @return void
     */
    protected function registerTranslator()
    {
        $this->getContainer()->share('translator', Translator::class)
            ->withArgument(AbstractBackend::class);
    }

    /**
     * Register the translation line loader.
     *
     * @return void
     */
    protected function registerLoader()
    {
        $this->getContainer()->share('translation.loader', function () {
            $backend = $this->createFileBackend();
            return $backend;
        });

        $this->getContainer()->alias('translation.loader', AbstractBackend::class);
    }

    protected function registerLanguages()
    {
        $this->getContainer()->share('translation.languages', function () {
            $languages = config('app.locale.enabled');
            return is_array($languages) ? $languages : explode(',', $languages);
        });

    }

    protected function createFileBackend()
    {
        $backend = $this->getContainer()->get(File::class);
        $languages = $this->getContainer()->get('translation.languages');

        foreach ($languages as $lang) {
            $backend->addLanguage($lang, app('path.lang') . DIRECTORY_SEPARATOR . $lang);
        }
    }

    /**
     * @inheritdoc
     */
    public function provides()
    {
        return ['translator', 'translator.languages', 'translation.loader'];
    }
}