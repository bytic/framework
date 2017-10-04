<?php

namespace Nip\Tests\Container;

use Mockery as m;
use Nip\Container\Container;

/**
 * Class ContainerTest
 * @package Nip\Tests\Container
 */
class ContainerTest extends \Nip\Tests\AbstractTest
{
    // tests

    public function testSetsAndGetServiceDefaultNotShared()
    {
        $container = new Container;

        $container->add('service', '\stdClass');
        static::assertTrue($container->has('service'));

        $service1 = $container->get('service');
        $service2 = $container->get('service');

        static::assertInstanceOf('\stdClass', $service1, '->assert service init');
        static::assertInstanceOf('\stdClass', $service2, '->assert service init');
        static::assertNotSame($service1, $service2, '->assert not shared by default');
    }

    public function testSetsAndGetServiceShared()
    {
        $container = new Container;

        $container->add('service', '\stdClass', true);
        static::assertTrue($container->has('service'));

        $service1 = $container->get('service');
        $service2 = $container->get('service');

        static::assertInstanceOf('\stdClass', $service1, '->assert service init');
        static::assertInstanceOf('\stdClass', $service2, '->assert service init');
        static::assertSame($service1, $service2, '->assert shared');
    }

    /**
     * Asserts that the container sets and gets an instance as shared.
     */
    public function testSetsAndGetInstanceAsShared()
    {
        $container = new Container;
        $class = new \stdClass;
        $container->add('service', $class);
        static::assertTrue($container->has('service'));
        static::assertSame($container->get('service'), $class);
    }
}
