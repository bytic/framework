<?php

namespace Nip\Tests;

class DispatcherTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var \Nip\Dispatcher
     */
    protected $_object;

    protected function _before()
    {
        $this->_object = new \Nip\Dispatcher();
    }

    protected function _after()
    {
    }

    // tests
    public function testDispatch()
    {
    }


}