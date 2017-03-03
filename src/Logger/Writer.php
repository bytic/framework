<?php

namespace Nip\Logger;

use Monolog\Logger as MonologLogger;
use Nip\Application;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

/**
 * Class Manager
 *
 * @package Nip\Logger
 *
 */
class Writer implements PsrLoggerInterface
{
    /**
     * The Log levels.
     *
     * @var array
     */

    const EMERGENCY = MonologLogger::EMERGENCY;
    const ALERT = MonologLogger::ALERT;
    const CRITICAL = MonologLogger::CRITICAL;
    const ERROR = MonologLogger::ERROR;
    const WARNING = MonologLogger::WARNING;
    const NOTICE = MonologLogger::NOTICE;
    const INFO = MonologLogger::INFO;
    const DEBUG = MonologLogger::DEBUG;

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
     * System is unusable.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function emergency($message, array $context = [])
    {
        return $this->log(self::EMERGENCY, $message, $context);
    }

    /**
     * @param mixed $level
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
     * @return bool
     */
    protected function writeLog($level, $message, $context)
    {
        return $this->getMonolog()->addRecord($level, $message, $context);
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function alert($message, array $context = [])
    {
        return $this->log(self::ALERT, $message, $context);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function critical($message, array $context = [])
    {
        return $this->log(self::CRITICAL, $message, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function error($message, array $context = [])
    {
        return $this->log(self::ERROR, $message, $context);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function warning($message, array $context = [])
    {
        return $this->log(self::WARNING, $message, $context);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function notice($message, array $context = [])
    {
        return $this->log(self::NOTICE, $message, $context);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function info($message, array $context = [])
    {
        return $this->log(self::INFO, $message, $context);
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function debug($message, array $context = [])
    {
        return $this->log(self::DEBUG, $message, $context);
    }
}
