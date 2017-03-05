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

        static::assertEquals('\Nip\Helpers\View\Messages', $view->getHelperClass('Messages'));
        static::assertEquals('\Nip\Helpers\View\Paginator', $view->getHelperClass('Paginator'));
        static::assertEquals('\Nip\Helpers\View\Scripts', $view->getHelperClass('Scripts'));
        static::assertEquals('\Nip\Helpers\View\TinyMCE', $view->getHelperClass('TinyMCE'));
    }

    public function testDynamicCallHelper()
    {
        $view = new View();

        static::assertInstanceOf('Nip\Helpers\View\Messages', $view->Messages());
        static::assertInstanceOf('Nip\Helpers\View\Paginator', $view->Paginator());
        static::assertInstanceOf('Nip\Helpers\View\Scripts', $view->Scripts());
        static::assertInstanceOf('Nip\Helpers\View\TinyMCE', $view->TinyMCE());
    }

    // tests

    public function testHelperInjectView()
    {
        $view = new View();

        static::assertInstanceOf('Nip\View', $view->Messages()->getView());
        static::assertInstanceOf('Nip\View', $view->Paginator()->getView());
        static::assertInstanceOf('Nip\View', $view->Scripts()->getView());
    }

    protected function _before()
    {
    }

    protected function _after()
    {
    }
}