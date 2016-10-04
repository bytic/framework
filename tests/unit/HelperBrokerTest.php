<?php

namespace Nip\Tests\Unit;

use Nip\HelperBroker;

class HelperBrokerTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function testGetHelperClass()
    {
        $broker = new HelperBroker();
        $this->assertEquals('Nip_Helper_Url', $broker->getHelperClass('Url'));
        $this->assertEquals('Nip_Helper_XML', $broker->getHelperClass('XML'));
        $this->assertEquals('Nip_Helper_Passwords', $broker->getHelperClass('passwords'));

    }

    public function testGenerateHelper()
    {
        $broker = new HelperBroker();

        $this->assertInstanceOf('Nip_Helper_Url', $broker->generateHelper('Url'));
        $this->assertInstanceOf('Nip_Helper_XML', $broker->generateHelper('XML'));
        $this->assertInstanceOf('Nip_Helper_Passwords', $broker->generateHelper('passwords'));
    }

    // tests

    public function testGet()
    {
        $this->assertInstanceOf('Nip_Helper_Url', HelperBroker::get('Url'));
        $this->assertInstanceOf('Nip_Helper_XML', HelperBroker::get('XML'));
        $this->assertInstanceOf('Nip_Helper_Passwords', HelperBroker::get('passwords'));
    }

    protected function _before()
    {
    }

    protected function _after()
    {
    }

}