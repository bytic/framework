<?php

namespace Nip\Tests\FlashData;

use Nip\Container\Container;
use Nip\FlashData\FlashData;
use Nip\FlashData\FlashMessages;
use Nip\FlashData\FlashServiceProvider;
use Nip\Tests\AbstractTest;

/**
 * Class FlashServiceProviderTest
 * @package Nip\Tests\FlashData
 */
class FlashServiceProviderTest extends AbstractTest
{
    public function testRegisterLoader()
    {
        $container = Container::getInstance();
        $provider = new FlashServiceProvider();
        $provider->setContainer($container);
        $provider->register();

        $flashMessages = $container->get('flash.messages');

        self::assertInstanceOf(FlashMessages::class, $flashMessages);
        self::assertInstanceOf(FlashData::class, $container->get('flash.data'));
    }
}
