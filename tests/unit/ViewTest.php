<?php

namespace Nip\Tests;

use Nip\View;

class ViewTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests

    public function testGetHelperClass()
    {
        $view = new Nip_View();

        $this->assertEquals('Nip_Helper_View_Messages', $view->getHelperClass('Messages'));
        $this->assertEquals('Nip_Helper_View_Paginator', $view->getHelperClass('Paginator'));
        $this->assertEquals('Nip_Helper_View_Scripts', $view->getHelperClass('Scripts'));
        $this->assertEquals('Nip_Helper_View_TinyMCE', $view->getHelperClass('TinyMCE'));
    }

    public function testDynamicCallHelper()
    {
        $view = new Nip_View();

        $this->assertInstanceOf('Nip_Helper_View_Messages', $view->Messages());
        $this->assertInstanceOf('Nip_Helper_View_Paginator', $view->Paginator());
        $this->assertInstanceOf('Nip_Helper_View_Scripts', $view->Scripts());
        $this->assertInstanceOf('Nip_Helper_View_TinyMCE', $view->TinyMCE());
    }

    public function testHelperInjectView()
    {
        $view = new Nip_View();

        $this->assertInstanceOf('Nip_View', $view->Messages()->getView());
        $this->assertInstanceOf('Nip_View', $view->Paginator()->getView());
        $this->assertInstanceOf('Nip_View', $view->Scripts()->getView());
    }
}