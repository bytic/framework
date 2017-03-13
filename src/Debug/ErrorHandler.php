<?php

namespace Nip\Debug;

use Symfony\Component\Debug\ErrorHandler as SymfonyErrorHandler;

/**
 * Class ErrorHandler
 * @package Nip\Debug
 */
class ErrorHandler extends SymfonyErrorHandler
{
//    private $levels = [
//        E_DEPRECATED => 'Deprecated',
//        E_USER_DEPRECATED => 'User Deprecated',
//        E_NOTICE => 'Notice',
//        E_USER_NOTICE => 'User Notice',
//        E_STRICT => 'Runtime Notice',
//        E_WARNING => 'Warning',
//        E_USER_WARNING => 'User Warning',
//        E_COMPILE_WARNING => 'Compile Warning',
//        E_CORE_WARNING => 'Core Warning',
//        E_USER_ERROR => 'User Error',
//        E_RECOVERABLE_ERROR => 'Catchable Fatal Error',
//        E_COMPILE_ERROR => 'Compile Error',
//        E_PARSE => 'Parse Error',
//        E_ERROR => 'Error',
//        E_CORE_ERROR => 'Core Error',
//    ];
//
//    private $loggers = [
//        E_DEPRECATED => [null, LogLevel::INFO],
//        E_USER_DEPRECATED => [null, LogLevel::INFO],
//        E_NOTICE => [null, LogLevel::WARNING],
//        E_USER_NOTICE => [null, LogLevel::WARNING],
//        E_STRICT => [null, LogLevel::WARNING],
//        E_WARNING => [null, LogLevel::WARNING],
//        E_USER_WARNING => [null, LogLevel::WARNING],
//        E_COMPILE_WARNING => [null, LogLevel::WARNING],
//        E_CORE_WARNING => [null, LogLevel::WARNING],
//        E_USER_ERROR => [null, LogLevel::CRITICAL],
//        E_RECOVERABLE_ERROR => [null, LogLevel::CRITICAL],
//        E_COMPILE_ERROR => [null, LogLevel::CRITICAL],
//        E_PARSE => [null, LogLevel::CRITICAL],
//        E_ERROR => [null, LogLevel::CRITICAL],
//        E_CORE_ERROR => [null, LogLevel::CRITICAL],
//    ];
//
//    private $thrownErrors = 0x1FFF; // E_ALL - E_DEPRECATED - E_USER_DEPRECATED
//    private $scopedErrors = 0x1FFF; // E_ALL - E_DEPRECATED - E_USER_DEPRECATED
//    private $tracedErrors = 0x77FB; // E_ALL - E_STRICT - E_PARSE
//    private $screamedErrors = 0x55; // E_ERROR + E_CORE_ERROR + E_COMPILE_ERROR + E_PARSE
//    private $loggedErrors = 0;
//    private $traceReflector;
//
//    private $isRecursive = 0;
//    private $isRoot = false;
//    private $exceptionHandler;
//    private $bootstrappingLogger;
//
//    private static $reservedMemory;
//    private static $stackedErrors = [];
//    private static $stackedErrorLevels = [];
//    private static $toStringException = null;
}
