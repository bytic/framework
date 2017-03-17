<?php

namespace Nip\Locale;

use Nip\Container\ServiceProviders\Providers\AbstractSignatureServiceProvider;

/**
 * Class LocaleServiceProvider
 * @package Nip\Mail
 */
class LocaleServiceProvider extends AbstractSignatureServiceProvider
{

    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->registerLocale();
    }

    protected function registerLocale()
    {
        $this->getContainer()->share('locale', Locale::class);
    }

    /**
     * @inheritdoc
     */
    public function provides()
    {
        return ['locale'];
    }
}
