<?php

namespace Nip\Tests\Unit\Helpers\View;

use Mockery as m;
use Nip\Helpers\View\GoogleAnalytics;

class GoogleAnalyticsTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var GoogleAnalytics
     */
    protected $_object;

    protected $_ua = '';
    protected $_domain = 'galantom.loc';

    public function testAddOperation()
    {
        $data = array(
            'orderId' => 1,
            'amount' => 100,
        );
        $this->_object->addTransaction($data);

        $response = array(
            1 => (object) $data
        );

        $this->assertEquals($this->_object->getTransactions(), $response);
    }

    protected function _before()
    {
        $flashMock = m::mock('Nip_Flash')->shouldDeferMissing();

        $this->_object = new GoogleAnalytics();
        $this->_object->setFlashMemory($flashMock);
        $this->_object->setUA($this->_ua);
        $this->_object->setDomain($this->_domain);
    }

    // tests

    protected function _after()
    {
    }
}