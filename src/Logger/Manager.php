<?php

namespace Nip\Logger;

use Monolog\Logger as MonologLogger;
use Nip\Application;
use Psr\Log\LoggerInterface as PsrLoggerInterface;
use Psr\Log\LoggerTrait;

/**
 * Class Manager.
 */
class Manager implements PsrLoggerInterface
{
    use LoggerTrait;

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
     * Map native PHP errors to level.
     *
     * @var array
     */
    public static $errorLevelMap = [
        E_NOTICE            => self::NOTICE,
        E_USER_NOTICE       => self::NOTICE,
        E_WARNING           => self::WARNING,
        E_CORE_WARNING      => self::WARNING,
        E_USER_WARNING      => self::WARNING,
        E_ERROR             => self::WARNING,
        E_USER_ERROR        => self::ERROR,
        E_CORE_ERROR        => self::ERROR,
        E_RECOVERABLE_ERROR => self::ERROR,
        E_PARSE             => self::ERROR,
        E_COMPILE_ERROR     => self::ERROR,
        E_COMPILE_WARNING   => self::ERROR,
        E_STRICT            => self::DEBUG,
        E_DEPRECATED        => self::DEBUG,
        E_USER_DEPRECATED   => self::DEBUG,
    ];

    /**
     * Registered error handler.
     *
     * @var bool
     */
    protected static $registeredErrorHandler = false;

    /**
     * Registered exception handler.
     *
     * @var bool
     */
    protected static $registeredExceptionHandler = false;

    /**
     * container for the Monolog instance.
     *
     * @var \Monolog\Logger
     */
    protected $monolog = null;

    /**
     * @var Application
     */
    protected $bootstrap;

    /**
     * Unregister error handler.
     */
    public static function unregisterErrorHandler()
    {
        restore_error_handler();
        static::$registeredErrorHandler = false;
    }

    /**
     * Unregister exception handler.
     */
    public static function unregisterExceptionHandler()
    {
        restore_exception_handler();
        static::$registeredExceptionHandler = false;
    }

    public function init()
    {
        $this->initStreams();
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
     * @inheritdoc
     */
    public function log($level, $message, array $context = [])
    {
        $this->writeLog($level, $message, $context);
    }

    /**
     * Write a message to Monolog.
     *
     * @param string $level
     * @param string $message
     * @param array  $context
     *
     * @return bool
     */
    protected function writeLog($level, $message, $context)
    {
        return $this->getMonolog()->{$level}($message, $context);
    }
}
