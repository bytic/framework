<?php

namespace Nip\Tests\Unit;

use Nip\Controller;

/**
 * Class ControllerTest
 * @package Nip\Tests\Unit
 */
class ControllerTest extends AbstractTest
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function testDynamicCallHelper()
    {
        $controller = new Controller();

        static::assertInstanceOf('Nip_Helper_Url', $controller->Url());
        static::assertInstanceOf('Nip_Helper_Xml', $controller->Xml());
        static::assertInstanceOf('Nip_Helper_Passwords', $controller->Passwords());
    }

    public function testGetHelper()
    {
        $controller = new Controller();

        static::assertInstanceOf('Nip_Helper_Url', $controller->getHelper('Url'));
        static::assertInstanceOf('Nip_Helper_Xml', $controller->getHelper('Xml'));
        static::assertInstanceOf('Nip_Helper_Passwords', $controller->getHelper('passwords'));
    }

    // tests

    protected function _before()
    {
    }

    protected function _after()
    {
    }
}