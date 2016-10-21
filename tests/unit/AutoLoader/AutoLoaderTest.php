<?php

namespace Nip\Tests\Unit\AutoLoader;

use Mockery as m;
use Nip\AutoLoader\AutoLoader;
use Nip\Tests\Unit\AbstractTest;

/**
 * Class ContainerTest
 * @package Nip\Tests\Unit\Container
 */
class AutoLoaderTest extends AbstractTest
{

    /**
     * @var AutoLoader
     */
    protected $object;

    // tests

    public function testRegisterHandler()
    {
        $mock = m::mock(AutoLoader::class)
            ->shouldReceive('autoload')->with('FictiveClass')->andReturn(true)->times(2)
            ->getMock();
        self::assertInstanceOf(AutoLoader::class, $mock);

        AutoLoader::registerHandler($mock);

        self::assertTrue($mock->autoload('FictiveClass'));
        self::assertFalse(class_exists('FictiveClass'));
    }

    protected function setUp()
    {
        parent::setUp();
        $this->object = new AutoLoader();
    }
}