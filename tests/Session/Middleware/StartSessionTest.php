<?php

namespace Nip\Tests\Session\Middleware;

use Nip\Application;
use Nip\Http\Response\Response;
use Nip\Http\ServerMiddleware\Dispatcher;
use Nip\Request;
use Nip\Session\Middleware\StartSession;
use Nip\Session\SessionManager;
use Nip\Tests\AbstractTest;

/**
 * Class DebugbarMiddlewareTest
 * @package Nip\DebugBar\Tests\Middleware
 */
class StartSessionTest extends AbstractTest
{
    public function testProcess()
    {
        $sessionManager = new SessionManager(new Application());

        $dispatcher = new Dispatcher(
            [
                new StartSession($sessionManager),
                function () {
                    return (new Response())->setContent('test');
                },
            ]
        );

        /** @var Response $response */
        $response = $dispatcher->dispatch(new Request());

        self::assertInstanceOf(Response::class, $response);
    }
}
