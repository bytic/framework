<?php

namespace Nip\Logger;

use ErrorException;
use Monolog\Logger as MonologLogger;
use Nip\Application;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

/**
 * Class Manager.
 */
class Manager implements PsrLoggerInterface
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
        $this->initErrorReporting();
        $this->initErrorDisplay();
        $this->registerHandler();
        $this->initStreams();
    }

    public function initErrorReporting()
    {
        error_reporting(E_ALL ^ E_NOTICE);
    }

    public function initErrorDisplay()
    {
        if ($this->getBootstrap()->getStaging()->getStage()->inTesting()) {
            ini_set('html_errors', 1);
            ini_set('display_errors', 1);
        } else {
            ini_set('display_errors', 0);
        }
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

    public function registerHandler()
    {
        self::registerErrorHandler($this);
        self::registerExceptionHandler($this);
    }

    /**
     * Register logging system as an error handler to log PHP errors
     * Based on Zend Logger.
     *
     * @param Manager $logger
     *
     * @throws Exception if logger is null
     *
     * @return mixed Returns result of set_error_handler
     */
    public static function registerErrorHandler(self $logger)
    {
        // Only register once per instance
        if (static::$registeredErrorHandler) {
            return false;
        }
        if ($logger === null) {
            throw new Exception('Invalid Logger specified');
        }

        $previous = set_error_handler([$logger, 'handleError']);
        static::$registeredErrorHandler = true;

        return $previous;
    }

    /**
     * Register logging system as an exception handler to log PHP exceptions
     * Based on Zend Logger.
     *
     * @param Manager $logger
     *
     * @throws Exception if logger is null
     *
     * @return bool
     */
    public static function registerExceptionHandler(self $logger)
    {
        // Only register once per instance
        if (static::$registeredExceptionHandler) {
            return false;
        }
        if ($logger === null) {
            throw new Exception('Invalid Logger specified');
        }
        set_exception_handler([$logger, 'handleException']);

        static::$registeredExceptionHandler = true;

        return true;
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
     * System is unusable.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function emergency($message, array $context = [])
    {
        return $this->log(self::EMERGENCY, $message, $context);
    }

    /**
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     *
     * @return bool|void
     */
    public function log($level, $message, array $context = [])
    {
        return $this->writeLog($level, $message, $context);
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
        return $this->getMonolog()->addRecord($level, $message, $context);
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array  $context
     *
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
     * @param array  $context
     *
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
     * @param array  $context
     *
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
     * @param array  $context
     *
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
     * @param array  $context
     *
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
     * @param array  $context
     *
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
     * @param array  $context
     *
     * @return null
     */
    public function debug($message, array $context = [])
    {
        return $this->log(self::DEBUG, $message, $context);
    }

    /**
     * @param $level
     * @param $message
     * @param $file
     * @param $line
     *
     * @return bool
     */
    public function handleError($level, $message, $file, $line)
    {
        $iniLevel = error_reporting();
        $errorLevelMap = static::$errorLevelMap;

        if ($iniLevel & $level) {
            if (isset($errorLevelMap[$level])) {
                $level = $errorLevelMap[$level];
            } else {
                $level = self::INFO;
            }
            $trace = debug_backtrace();

            $this->log($level, $message, [
                'errno' => $level,
                'file'  => $file,
                'line'  => $line,
                'trace' => $trace,
            ]);
        }

        return true;
    }

    /**
     * @private
     *
     * @param \Throwable $e
     */
    public function handleException(\Throwable $e)
    {
        $errorLevelMap = static::$errorLevelMap;
        $logMessages = [];
        do {
            $level = self::ERROR;
            if ($e instanceof ErrorException && isset($errorLevelMap[$e->getSeverity()])) {
                $level = $errorLevelMap[$e->getSeverity()];
            }
            $extra = [
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
                'trace' => $e->getTrace(),
            ];
            if (isset($e->xdebug_message)) {
                $extra['xdebug'] = $e->xdebug_message;
            }
            $logMessages[] = [
                'level'   => $level,
                'message' => $e->getMessage(),
                'extra'   => $extra,
            ];
            $e = $e->getPrevious();
        } while ($e);

        foreach (array_reverse($logMessages) as $logMessage) {
            $this->log($logMessage['level'], $logMessage['message'], $logMessage['extra']);
        }
    }
}
