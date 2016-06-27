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
        $this->_object = new Nip\Dispatcher();
    }

    protected function _after()
    {
    }

    // tests
    public function testDispatch()
    {
        foreach (array('production', 'staging', 'demo') as $stage) {
            $this->assertTrue($this->_object->isInPublicStages($stage));
        }
        $this->assertFalse($this->_object->isInPublicStages('local'));
        $this->assertFalse($this->_object->isInPublicStages('localhost'));
    }


}