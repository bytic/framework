<?php

namespace Nip\FlashData;

use Nip\Container\ServiceProviders\Providers\AbstractSignatureServiceProvider;

/**
 * Class FlashServiceProvider
 * @package Nip\FlashData
 */
class FlashServiceProvider extends AbstractSignatureServiceProvider
{

    /**
     * @inheritdoc
     */
    public function provides()
    {
        return ['flash.data', 'flash.messages'];
    }

    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->getContainer()->share('flash.data', FlashData::class);
        $this->getContainer()->share('flash.messages', FlashMessages::class);
    }
}
