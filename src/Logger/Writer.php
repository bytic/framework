<?php

namespace Nip\Logger;

use Monolog\Logger as MonologLogger;
use Nip\Application;
use Psr\Log\LoggerInterface as PsrLoggerInterface;
use Psr\Log\LoggerTrait;

/**
 * Class Manager
 *
 * @package Nip\Logger
 *
 */
class Writer implements PsrLoggerInterface
{
    use LoggerTrait;

    /**
     * container for the Monolog instance
     * @var \Monolog\Logger
     */
    protected $monolog = null;

    /**
     * @var Application
     */
    protected $bootstrap;


    /**
     * Create a new log writer instance.
     *
     * @param  \Monolog\Logger $monolog
     */
    public function __construct(MonologLogger $monolog)
    {
        $this->setMonolog($monolog);
    }

    public function init()
    {
        $this->initStreams();
    }

    public function initStreams()
    {
        $streams = $this->getStreams();
        foreach ($streams as $stream) {
            $this->getMonolog()->pushHandler($stream);
        }
    }

    /**
     * @return array
     */
    public function getStreams()
    {
        return [];
    }

    /**
     * @return MonologLogger
     */
    public function getMonolog()
    {
        if ($this->monolog == null) {
            $this->initMonolog();
        }

        return $this->monolog;
    }

    /**
     * @param MonologLogger $monolog
     */
    public function setMonolog($monolog)
    {
        $this->monolog = $monolog;
    }

    public function initMonolog()
    {
        $monolog = new MonologLogger('Nip');
        $this->setMonolog($monolog);
    }

    /**
     * @return Application
     */
    public function getBootstrap()
    {
        return $this->bootstrap;
    }

    /**
     * @param Application $bootstrap
     */
    public function setBootstrap($bootstrap)
    {
        $this->bootstrap = $bootstrap;
    }

    /**
     * @param string $level
     * @param string $message
     * @param array $context
     * @return bool|void
     */
    public function log($level, $message, array $context = [])
    {
        $this->writeLog($level, $message, $context);
    }

    /**
     * Write a message to Monolog.
     *
     * @param  string $level
     * @param  string $message
     * @param  array $context
     * @return void
     */
    protected function writeLog($level, $message, $context)
    {
        $this->getMonolog()->{$level}($message, $context);
    }
}
