<?php

namespace Nip\Tests;

class RequestTest extends \Codeception\TestCase\Test
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
        $this->_object = new \Nip_Request();
    }

    protected function _after()
    {
    }

    // tests
    public function testConstructor()
    {
        $_GET['var1'] = 'value1';
        $_GET['var2'] = 'value2';
        $_POST['var3'] = 'value3';
        $_POST['var4'] = 'value4';
        foreach ($_GET as $key => $stage) {
            $this->assertTrue($this->_object->isInPublicStages($stage));
        }
        $this->assertFalse($this->_object->isInPublicStages('local'));
        $this->assertFalse($this->_object->isInPublicStages('localhost'));
    }


}