<?php

namespace Nip\Tests\Unit;

use Nip\Application;
use Nip\Mail\Mailer;
use Nip\Mvc\Modules;

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
    }

    /**
     */
    protected function setUp()
    {
        parent::setUp();
        $this->application = new Application();
    }
}
