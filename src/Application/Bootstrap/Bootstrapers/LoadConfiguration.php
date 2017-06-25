<?php

namespace Nip\Application\Bootstrap\Bootstrapers;

use Nip\Application;
use Nip\Config\Config;
use Nip\Config\Factory;
use Symfony\Component\Finder\Finder;

/**
 * Class LoadConfiguration
 *
 * @package Nip\Application\Bootstrap\Bootstrapers
 */
class LoadConfiguration extends AbstractBootstraper
{
    /**
     * Bootstrap the given application.
     *
     * @param Application $app
     * @return void
     */
    public function bootstrap(Application $app)
    {
        $items = [];
        // First we will see if we have a cache configuration file. If we do, we'll load
        // the configuration items from that file so that it is very quick. Otherwise
        // we will need to spin through every configuration file and load them all.
//        if (file_exists($cached = $app->getCachedConfigPath())) {
//            $items = require $cached;
//            $loadedFromCache = true;
//        }

        // Next we will spin through all of the configuration files in the configuration
        // directory and load each one into the repository. This will make all of the
        // options available to the developer for use in various parts of this app.
        $app->share('config', $config = new Config($items, true));

//        if (!isset($loadedFromCache)) {
        $this->loadConfigurationFiles($app, $config);
//        }
        // Finally, we will set the application's environment based on the configuration
        // values that were loaded. We will pass a callback which will be used to get
        // the environment in a web context where an "--env" switch is not present.
//        $app->detectEnvironment(function () use ($config) {
//            return $config->get('app.env', 'production');
//        });
        date_default_timezone_set($config->get('app.timezone', 'UTC'));
        mb_internal_encoding('UTF-8');
    }

    /**
     * Load the configuration items from all of the files.
     *
     * @param  Application $app
     * @param  Config $repository
     * @return void
     */
    protected function loadConfigurationFiles(Application $app, Config $repository)
    {
        Factory::fromFiles($repository, $this->getConfigurationFiles($app));
    }

    /**
     * Get all of the configuration files for the application.
     *
     * @param Application $app
     * @return array
     */
    protected function getConfigurationFiles(Application $app)
    {
        $files = [];
        $configPath = realpath($app->configPath());
        foreach (Finder::create()->files()->name('*.php')->in($configPath) as $file) {
            $files[basename($file->getRealPath(), '.php')] = $file->getRealPath();
        }
        return $files;
    }
}
