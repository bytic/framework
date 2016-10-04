<?php

namespace Nip\Tests\Unit;

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

    public function testDispatch()
    {
    }

    protected function _before()
    {
        $this->_object = new \Nip\Dispatcher();
    }

    // tests

    protected function _after()
    {
    }


}