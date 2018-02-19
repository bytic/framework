<?php

namespace Nip\Application\Bootstrap\Bootstrapers;

use Dotenv\Dotenv;
use Dotenv\Exception\InvalidPathException;
use Nip\Application;
use Symfony\Component\Console\Input\ArgvInput;

/**
 * Class LoadEnvironmentVariables
 *
 * @package Nip\Application\Bootstrap\Bootstrapers
 */
class LoadEnvironmentVariables extends AbstractBootstraper
{
    /**
     * Bootstrap the given application.
     *
     * @param  Application $app
     * @return void
     */
    public function bootstrap(Application $app)
    {
        if ($app->configurationIsCached()) {
            return;
        }

        $this->checkForSpecificEnvironmentFile($app);
        try {
            (new Dotenv($app->environmentPath(), $app->environmentFile()))->load();
        } catch (InvalidPathException $e) {
            //
        }
    }

    /**
     * Detect if a custom environment file matching the APP_ENV exists.
     *
     * @param  Application $app
     * @return void
     */
    protected function checkForSpecificEnvironmentFile($app)
    {
//        if (php_sapi_name() == 'cli' && with($input = new ArgvInput)->hasParameterOption('--env')) {
//            $this->setEnvironmentFilePath(
//                $app, $app->environmentFile() . '.' . $input->getParameterOption('--env')
//            );
//        }
        if (!env('APP_ENV') || empty($file)) {
            return;
        }
        $this->setEnvironmentFilePath(
            $app,
            $app->environmentFile().'.'.env('APP_ENV')
        );
    }

    /**
     * Load a custom environment file.
     *
     * @param  Application $app
     * @param  string $file
     * @return void
     */
    protected function setEnvironmentFilePath($app, $file)
    {
        if (file_exists($app->environmentPath().'/'.$file)) {
            $app->loadEnvironmentFrom($file);
        }
    }
}