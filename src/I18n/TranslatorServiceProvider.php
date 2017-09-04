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
    protected $languages = null;
    protected $languageDirectory = null;

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
            return $this->getLanguages();
        });
    }

    /**
     * @return File
     */
    protected function createFileBackend()
    {
        /** @var File $backend */
        $backend = $this->getContainer()->get(File::class);
        $backend->setBaseDirectory($this->getLanguageDirectory());

        $languages = $this->getContainer()->get('translation.languages');
        $backend->addLanguages($languages);

        return $backend;
    }

    /**
     * @inheritdoc
     */
    public function provides()
    {
        return ['translator', 'translation.languages', 'translation.loader'];
    }

    /**
     * @return null
     */
    public function getLanguages()
    {
        if ($this->languages === null) {
            $this->initLanguages();
        }

        return $this->languages;
    }

    protected function initLanguages()
    {
        $this->setLanguages($this->generateLanguages());
    }

    /**
     * @return array
     */
    protected function generateLanguages()
    {
        $languages = config('app.locale.enabled');

        return is_array($languages) ? $languages : explode(',', $languages);
    }

    /**
     * @param null $languages
     */
    public function setLanguages($languages)
    {
        $this->languages = $languages;
    }

    /**
     * @return null
     */
    public function getLanguageDirectory()
    {
        if ($this->languageDirectory === null) {
            $this->initLanguageDirectory();
        }

        return $this->languageDirectory;
    }

    /**
     * @param null $languageDirectory
     */
    public function setLanguageDirectory($languageDirectory)
    {
        $this->languageDirectory = $languageDirectory;
    }

    protected function initLanguageDirectory()
    {
        $this->setLanguageDirectory($this->generateLanguageDirectory());
    }

    /**
     * @return string
     */
    protected function generateLanguageDirectory()
    {
        return app('path.lang');
    }
}
