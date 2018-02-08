<?php

namespace Nip\Tests\Unit;

use Nip\HelperBroker;

/**
 * Class HelperBrokerTest
 * @package Nip\Tests\Unit
 */
class HelperBrokerTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function testGetHelperClass()
    {
        $broker = new HelperBroker();
        static::assertEquals('Nip_Helper_Url', $broker->getHelperClass('Url'));
        static::assertEquals('Nip_Helper_Url', $broker->getHelperClass('Url'));
        static::assertEquals('Nip_Helper_XML', $broker->getHelperClass('XML'));
        static::assertEquals('Nip_Helper_Passwords', $broker->getHelperClass('passwords'));
    }

    public function testGenerateHelper()
    {
        $broker = new HelperBroker();

        static::assertInstanceOf('Nip_Helper_Url', $broker->generateHelper('Url'));
        static::assertInstanceOf('Nip_Helper_XML', $broker->generateHelper('XML'));
        static::assertInstanceOf('Nip_Helper_Passwords', $broker->generateHelper('passwords'));
    }

    // tests

    public function testGet()
    {
        static::assertInstanceOf('Nip_Helper_Url', HelperBroker::get('Url'));
        static::assertInstanceOf('Nip_Helper_XML', HelperBroker::get('XML'));
        static::assertInstanceOf('Nip_Helper_Passwords', HelperBroker::get('passwords'));
    }

    protected function _before()
    {
    }

    protected function _after()
    {
    }
}
