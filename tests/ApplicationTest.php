<?php

namespace Nip\Tests;

use Nip\Application;

/**
 * Class ApplicationTest
 */
class ApplicationTest extends AbstractTest
{

    /**
     * @var Application
     */
    protected $application;

    public function testBooting()
    {
        $application = new Application();
        static::assertFalse($application->isBooted());
    }
}
