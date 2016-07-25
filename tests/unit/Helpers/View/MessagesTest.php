<?php

namespace Nip\Tests\Helpers\View;

use Mockery as m;
use Nip\Helpers\View\Messages;

class MessagesTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var Messages
     */
    protected $_object;

    protected function _before()
    {
        $this->_object = new Messages();
    }

    protected function _after()
    {
    }

    // tests

    public function testWarning()
    {
        $this->assertEquals(Messages::warning('messages'), '<div class="alert alert-warning">messages</div>');
        $this->assertEquals($this->_object->warning('messages'), '<div class="alert alert-warning">messages</div>');
    }

    public function testInfo()
    {
        $this->assertEquals(Messages::info('messages'), '<div class="alert alert-info">messages</div>');
        $this->assertEquals($this->_object->info('messages'), '<div class="alert alert-info">messages</div>');
    }
}