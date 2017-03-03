<?php

namespace Nip\Logger;

use Monolog\Logger as Monolog;
use Nip\Container\ServiceProviders\Providers\AbstractSignatureServiceProvider;
use Nip\Debug\ErrorHandler;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

/**
 * Class LoggerServiceProvider
 * @package Nip\Logger
 */
class LoggerServiceProvider extends AbstractSignatureServiceProvider
{
    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->getContainer()->share('log', function () {
            $logger = $this->createLogger();
            $this->getContainer()->get(ErrorHandler::class)->setDefaultLogger($logger);

            return $this->createLogger();
        });
    }

    /**
     * Create the logger.
     *
     * @return Writer
     */
    public function createLogger()
    {
        $log = new Writer(
            new Monolog($this->channel())
        );
//        if ($this->app->hasMonologConfigurator()) {
//            call_user_func($this->app->getMonologConfigurator(), $log->getMonolog());
//        } else {
//            $this->configureHandler($log);
//        }
        return $log;
    }

    /**
     * Get the name of the log "channel".
     *
     * @return string
     */
    protected function channel()
    {
        return 'production';
    }

    /**
     * @inheritdoc
     */
    public function provides()
    {
        return ['log', PsrLoggerInterface::class];
    }
}