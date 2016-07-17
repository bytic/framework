<?php

namespace Nip\Logger;

use ErrorException;
use Monolog\Logger as MonologLogger;
use Nip\Bootstrap;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

class Manager implements PsrLoggerInterface
{
    /**
     * container for the Monolog instance
     * @var \Monolog\Logger
     */
    protected $monolog = null;

    /**
     * @var Bootstrap
     */
    protected $bootstrap;

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
     * Map native PHP errors to level
     *
     * @var array
     */
    public static $errorLevelMap = [
        E_NOTICE => self::NOTICE,
        E_USER_NOTICE => self::NOTICE,
        E_WARNING => self::WARNING,
        E_CORE_WARNING => self::WARNING,
        E_USER_WARNING => self::WARNING,
        E_ERROR => self::WARNING,
        E_USER_ERROR => self::ERROR,
        E_CORE_ERROR => self::ERROR,
        E_RECOVERABLE_ERROR => self::ERROR,
        E_PARSE => self::ERROR,
        E_COMPILE_ERROR => self::ERROR,
        E_COMPILE_WARNING => self::ERROR,
        E_STRICT => self::DEBUG,
        E_DEPRECATED => self::DEBUG,
        E_USER_DEPRECATED => self::DEBUG,
    ];


    /**
     * Registered error handler
     *
     * @var bool
     */
    protected static $registeredErrorHandler = false;

    /**
     * Registered exception handler
     *
     * @var bool
     */
    protected static $registeredExceptionHandler = false;

    public function init()
    {
        if ($this->getBootstrap()->getStage()->inTesting()) {
            ini_set('html_errors', 1);
            ini_set('display_errors', 1);
            error_reporting(E_ALL ^ E_NOTICE);
        } else {
            ini_set('display_errors', 0);
            error_reporting(0);
        }

        self::registerErrorHandler($this);
        self::registerExceptionHandler($this);
    }

    /**
     * System is unusable.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function emergency($message, array $context = array())
    {
        return $this->log(self::EMERGENCY, $message, $context);
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
    public function alert($message, array $context = array())
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
    public function critical($message, array $context = array())
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
    public function error($message, array $context = array())
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
    public function warning($message, array $context = array())
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
    public function notice($message, array $context = array())
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
    public function info($message, array $context = array())
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
    public function debug($message, array $context = array())
    {
        return $this->log(self::DEBUG, $message, $context);
    }

    public function log($level, $message, array $context = array())
    {
        return $this->writeLog($level, $message, $context);
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
        $this->getMonolog()->addRecord($level, $message, $context);
    }

    /**
     * Register logging system as an error handler to log PHP errors
     * Based on Zend Logger
     *
     * @param Manager $logger
     * @param bool $continueNativeHandler
     * @return mixed  Returns result of set_error_handler
     * @throws Exception if logger is null
     */
    public static function registerErrorHandler(Manager $logger, $continueNativeHandler = false)
    {
        // Only register once per instance
        if (static::$registeredErrorHandler) {
            return false;
        }
        if ($logger === null) {
            throw new Exception('Invalid Logger specified');
        }
        $errorLevelMap = static::$errorLevelMap;

        $previous = set_error_handler(function ($level, $message, $file, $line) use ($logger, $errorLevelMap, $continueNativeHandler) {
            $iniLevel = error_reporting();
            if ($iniLevel & $level) {
                if (isset($errorLevelMap[$level])) {
                    $level = $errorLevelMap[$level];
                } else {
                    $level = Manager::INFO;
                }
                $logger->log($level, $message, [
                    'errno' => $level,
                    'file' => $file,
                    'line' => $line,
                ]);
            }
            return !$continueNativeHandler;
        });
        static::$registeredErrorHandler = true;
        return $previous;
    }

    /**
     * Unregister error handler
     *
     */
    public static function unregisterErrorHandler()
    {
        restore_error_handler();
        static::$registeredErrorHandler = false;
    }

    /**
     * Register logging system as an exception handler to log PHP exceptions
     * Based on Zend Logger
     *
     * @param Manager $logger
     * @return bool
     * @throws Exception if logger is null
     */
    public static function registerExceptionHandler(Manager $logger)
    {
        // Only register once per instance
        if (static::$registeredExceptionHandler) {
            return false;
        }
        if ($logger === null) {
            throw new Exception('Invalid Logger specified');
        }
        $errorLevelMap = static::$errorLevelMap;
        set_exception_handler(function ($exception) use ($logger, $errorLevelMap) {
            $logMessages = [];
            do {
                $level = Manager::ERROR;
                if ($exception instanceof ErrorException && isset($errorLevelMap[$exception->getSeverity()])) {
                    $level = $errorLevelMap[$exception->getSeverity()];
                }
                $extra = [
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'trace' => $exception->getTrace(),
                ];
                if (isset($exception->xdebug_message)) {
                    $extra['xdebug'] = $exception->xdebug_message;
                }
                $logMessages[] = [
                    'level' => $level,
                    'message' => $exception->getMessage(),
                    'extra' => $extra,
                ];
                $exception = $exception->getPrevious();
            } while ($exception);

            foreach (array_reverse($logMessages) as $logMessage) {
                $logger->log($logMessage['level'], $logMessage['message'], $logMessage['extra']);
            }
        });
        static::$registeredExceptionHandler = true;
        return true;
    }

    /**
     * Unregister exception handler
     */
    public static function unregisterExceptionHandler()
    {
        restore_exception_handler();
        static::$registeredExceptionHandler = false;
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

    public function initMonolog()
    {
        $monolog = new MonologLogger('Nip');
        $this->setMonolog($monolog);
    }

    /**
     * @param MonologLogger $monolog
     */
    public function setMonolog($monolog)
    {
        $this->monolog = $monolog;
    }

    /**
     * @return Bootstrap
     */
    public function getBootstrap()
    {
        return $this->bootstrap;
    }

    /**
     * @param Bootstrap $bootstrap
     */
    public function setBootstrap($bootstrap)
    {
        $this->bootstrap = $bootstrap;
    }
}