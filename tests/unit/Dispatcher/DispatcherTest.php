<?php

namespace Nip\Tests\Unit\Dispatcher;

use Nip\Dispatcher\Dispatcher;
use Nip\Tests\Unit\AbstractTest;

/**
 * Class DispatcherTest
 * @package Nip\Tests\Unit\Dispatcher
 */
class DispatcherTest extends AbstractTest
{

    /**
     * @var Dispatcher
     */
    protected $object;

    public function testDispatch()
    {
    }

    protected function setUp()
    {
        parent::setUp();
        $this->object = new Dispatcher();
    }
}
