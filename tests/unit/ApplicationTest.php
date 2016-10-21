<?php

namespace Nip\Tests\Unit;

use Nip\Application;
use Nip\Dispatcher\Dispatcher;
use Nip\Mail\Mailer;
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

        static::assertInstanceOf(Mailer::class, $this->application->getContainer()->get('mailer'));
        static::assertInstanceOf(Modules::class, $this->application->getContainer()->get('mvc.modules'));
        static::assertInstanceOf(Dispatcher::class, $this->application->getContainer()->get('dispatcher'));
        static::assertInstanceOf(Router::class, $this->application->getContainer()->get('router'));
    }

    /**
     */
    protected function setUp()
    {
        parent::setUp();
        $this->application = new Application();
    }
}
