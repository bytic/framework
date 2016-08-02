<?php
namespace Container;

use Mockery as m;
use Nip\Container\Container;

class ContainerTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests

    public function testSetsAndGetServiceDefaultNotShared()
    {
        $container = new Container;

        $container->add('service', '\stdClass');
        $this->assertTrue($container->has('service'));

        $service1 = $container->get('service');
        $service2 = $container->get('service');

        $this->assertInstanceOf('\stdClass', $service1, '->assert service init');
        $this->assertInstanceOf('\stdClass', $service2, '->assert service init');
        $this->assertNotSame($service1, $service2, '->assert not shared by default');
    }

    public function testSetsAndGetServiceShared()
    {
        $container = new Container;

        $container->add('service', '\stdClass', true);
        $this->assertTrue($container->has('service'));

        $service1 = $container->get('service');
        $service2 = $container->get('service');

        $this->assertInstanceOf('\stdClass', $service1, '->assert service init');
        $this->assertInstanceOf('\stdClass', $service2, '->assert service init');
        $this->assertSame($service1, $service2, '->assert shared');
    }

    /**
     * Asserts that the container sets and gets an instance as shared.
     */
    public function testSetsAndGetInstanceAsShared()
    {
        $container = new Container;
        $class = new \stdClass;
        $container->add('service', $class);
        $this->assertTrue($container->has('service'));
        $this->assertSame($container->get('service'), $class);
    }



    public function testSetAndGetServiceSharedOverwrite()
    {
        $container = new Container;

        $container->add('service', '\Nip\Mvc\Modules', true);
        $this->assertTrue($container->has('service'));

        $modules1 = $container->get('service');
        $this->assertInstanceOf('\Nip\Mvc\Modules', $modules1, '->assert service initial class');

        $container->add('service', '\stdClass', true);
        $modules2 = $container->get('service');
        $this->assertInstanceOf('\stdClass', $modules2, '->assert service overwrite');


        $container->add('service', new \stdClass);
        $modules3 = $container->get('service');
        $this->assertInstanceOf('\stdClass', $modules3, '->assert service overwrite');
        $this->assertNotSame($modules2, $modules3);

    }
}