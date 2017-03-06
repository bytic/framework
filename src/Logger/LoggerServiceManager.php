<?php

namespace Nip\Logger;

use Monolog\Logger as Monolog;
use Nip\Container\ServiceProviders\Providers\AbstractSignatureServiceProvider;
use Psr\Log\LoggerInterface as PsrLoggerInterface;
use Symfony\Component\Debug\ErrorHandler;

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
            $logger->init();
            $this->getContainer()->get(ErrorHandler::class)->setDefaultLogger($logger);

            return $logger;
        });

        $this->getContainer()->share(Monolog::class, $this->createMonolog());
    }

    /**
     * Create the logger.
     *
     * @return Writer
     */
    public function createLogger()
    {
        $log = $this->getContainer()->get(Writer::class);

//        if ($this->app->hasMonologConfigurator()) {
//            call_user_func($this->app->getMonologConfigurator(), $log->getMonolog());
//        } else {
//            $this->configureHandler($log);
//        }
        return $log;
    }

    /**
     * Create the Monolog.
     *
     * @return Monolog
     */
    protected function createMonolog()
    {
        return new Monolog($this->channel());
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
