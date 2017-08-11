<?php

namespace Nip\Tests\Form;

use Nip\Container\Container;
use Nip\Container\ContainerInterface;
use Nip\I18n\Translator\Backend\AbstractBackend;
use Nip\I18n\Translator\Backend\File;
use Nip\I18n\TranslatorServiceProvider;
use Nip\Tests\AbstractTest;

class TranslatorServiceProviderTest extends AbstractTest
{
    public function testRegisterLoader()
    {
        $provider = new TranslatorServiceProvider();
        $provider->setLanguages(['ro', 'en']);
        $provider->setLanguageDirectory(TEST_FIXTURE_PATH . DIRECTORY_SEPARATOR . 'languages');
        $container = Container::getInstance();
//        $container->addServiceProvider($provider);
        $provider->setContainer($container);
        $provider->register();

        $container = $provider->getContainer();
        self::assertInstanceOf(ContainerInterface::class, $container);

        self::assertInstanceOf(File::class, $container->get('translation.loader'));
        self::assertInstanceOf(File::class, $container->get(AbstractBackend::class));
    }

    protected function getContainer()
    {
        $container = Container::getInstance();
        $container->addServiceProvider(TranslatorServiceProvider::class);
        return $container;
    }

}
