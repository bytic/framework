<?php
use Nip\Application;

/**
 * Class ApplicationTest
 */
class ApplicationTest extends \Codeception\TestCase\Test
{

    /**
     * @var Application
     */
    protected $application;

    /**
     */
    public function _before()
    {
        $this->application = new Application();
    }

    public function testRegisterServices()
    {
        $this->application->registerServices();

        static::assertInstanceOf(\Nip\Mail\Mailer::class, $this->application->getContainer()->get('mailer'));
    }
}
