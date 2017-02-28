<?php

namespace Nip\Tests\Unit;

use Nip\Application;
use Nip\Container\Container;
use Nip\Dispatcher\Dispatcher;
use Nip\Mvc\Modules;
use Nip\Router\Router;

/**
 * Class ApplicationTest
 */
class ApplicationTest extends AbstractTest
{

    /**
     * @var Application
     */
    protected $application;

    public function testRegisterServices()
    {
        $this->application->registerServices();

//        static::assertInstanceOf(Mailer::class, $this->application->get('mailer'));
        static::assertInstanceOf(Modules::class, $this->application->get('mvc.modules'));
        static::assertInstanceOf(Dispatcher::class, $this->application->get('dispatcher'));
        static::assertInstanceOf(Router::class, $this->application->get('router'));
    }

    public function testContainerBindings()
    {
        $this->application->registerContainer();

        static::assertInstanceOf(Container::class, $this->application);
        static::assertSame(Container::getInstance(), $this->application);
    }

    /**
     */
    protected function setUp()
    {
        parent::setUp();
        $this->application = new Application();
    }
}
