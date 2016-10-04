<?php

namespace Nip\Tests\Unit;

use Nip\Controller;

class ControllerTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function testDynamicCallHelper()
    {
        $controller = new Controller();

        $this->assertInstanceOf('Nip_Helper_Url', $controller->Url());
        $this->assertInstanceOf('Nip_Helper_Xml', $controller->Xml());
        $this->assertInstanceOf('Nip_Helper_Passwords', $controller->Passwords());
    }

    public function testGetHelper()
    {
        $controller = new Controller();

        $this->assertInstanceOf('Nip_Helper_Url', $controller->getHelper('Url'));
        $this->assertInstanceOf('Nip_Helper_Xml', $controller->getHelper('Xml'));
        $this->assertInstanceOf('Nip_Helper_Passwords', $controller->getHelper('passwords'));
    }

    // tests

    protected function _before()
    {
    }

    protected function _after()
    {
    }
}