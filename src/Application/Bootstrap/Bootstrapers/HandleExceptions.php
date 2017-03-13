<?php

namespace Nip\Application\Bootstrap\Bootstrapers;

use Nip\Application;
use Nip\Debug\Debug;

/**
 * Class HandleExceptions
 * @package Nip\Application\Bootstrap\Bootstrapers
 */
class HandleExceptions extends AbstractBootstraper
{
    /**
     * Bootstrap the given application.
     *
     * @param Application $app
     * @return void
     */
    public function bootstrap(Application $app)
    {
        $this->setApp($app);

        error_reporting(-1);

        if (config('app.debug')) {
            Debug::enable(E_ALL, false);
        } else {
            Debug::enable(-1, false);
        }

//        $handler = set_error_handler('var_dump');
//        $handler = is_array($handler) ? $handler[0] : null;
//        restore_error_handler();
//
//        if ($handler instanceof ErrorHandler) {
//            $app->getContainer()->share(ErrorHandler::class, $handler);
//        }
    }
}
