<?php

namespace Nip\I18n;

use Nip\Container\ServiceProviders\Providers\AbstractSignatureServiceProvider;
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
            ->withArgument(File::class);
    }

    /**
     * Register the translation line loader.
     *
     * @return void
     */
    protected function registerLoader()
    {
        $this->getContainer()->share('translation.loader', function () {
            $backend = new File();
            $languages = config('app.locale.enabled');
            $languages = is_array($languages) ? $languages : explode(',', $languages);

            foreach ($languages as $lang) {
                $backend->addLanguage($lang, app('path.lang') . DIRECTORY_SEPARATOR . $lang);
            }
            return $backend;
        });

        $this->getContainer()->alias('translation.loader', File::class);
    }

    /**
     * @inheritdoc
     */
    public function provides()
    {
        return ['translator', 'translation.loader'];
    }
}