<?php

namespace Nip\Tests\Unit;

use Nip\View;

class ViewTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function testGetHelperClass()
    {
        $view = new View();

        $this->assertEquals('\Nip\Helpers\View\Messages', $view->getHelperClass('Messages'));
        $this->assertEquals('\Nip\Helpers\View\Paginator', $view->getHelperClass('Paginator'));
        $this->assertEquals('\Nip\Helpers\View\Scripts', $view->getHelperClass('Scripts'));
        $this->assertEquals('\Nip\Helpers\View\TinyMCE', $view->getHelperClass('TinyMCE'));
    }

    public function testDynamicCallHelper()
    {
        $view = new View();

        $this->assertInstanceOf('Nip\Helpers\View\Messages', $view->Messages());
        $this->assertInstanceOf('Nip\Helpers\View\Paginator', $view->Paginator());
        $this->assertInstanceOf('Nip\Helpers\View\Scripts', $view->Scripts());
        $this->assertInstanceOf('Nip\Helpers\View\TinyMCE', $view->TinyMCE());
    }

    // tests

    public function testHelperInjectView()
    {
        $view = new View();

        $this->assertInstanceOf('Nip\View', $view->Messages()->getView());
        $this->assertInstanceOf('Nip\View', $view->Paginator()->getView());
        $this->assertInstanceOf('Nip\View', $view->Scripts()->getView());
    }

    protected function _before()
    {
    }

    protected function _after()
    {
    }
}