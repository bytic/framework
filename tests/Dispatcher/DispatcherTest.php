<?php

namespace Nip\Tests\Dispatcher;

use Nip\Dispatcher\Dispatcher;
use Nip\Tests\AbstractTest;

/**
 * Class DispatcherTest
 * @package Nip\Tests\Dispatcher
 */
class DispatcherTest extends AbstractTest
{

    /**
     * @var Dispatcher
     */
    protected $object;

    public function testReverseControllerName()
    {
        self::assertSame('event-manager',Dispatcher::reverseControllerName('event_manager'));
        self::assertSame('event_manager',Dispatcher::reverseControllerName('event-manager'));
    }

    protected function setUp()
    {
        parent::setUp();
        $this->object = new Dispatcher();
    }
}
