<?php

namespace Nip\Debug;

use Symfony\Component\Debug\BufferingLogger;
use Symfony\Component\Debug\Debug as SymfonyDebug;

/**
 * Class Debug
 * @package Nip\Debug
 */
class Debug extends SymfonyDebug
{
    private static $enabled = false;

    /** @noinspection PhpMissingParentCallCommonInspection
     * Enables the debug tools.
     *
     * This method registers an error handler and an exception handler.
     *
     * If the Symfony ClassLoader component is available, a special
     * class loader is also registered.
     *
     * @param int $errorReportingLevel The level of error reporting you want
     * @param bool $displayErrors Whether to display errors (for development) or just log them (for production)
     */
    public static function enable($errorReportingLevel = E_ALL, $displayErrors = true)
    {
        if (static::$enabled) {
            return;
        }

        static::$enabled = true;

        if (null !== $errorReportingLevel) {
            error_reporting($errorReportingLevel);
        } else {
            error_reporting(E_ALL);
        }

        if ('cli' !== PHP_SAPI) {
            ini_set('display_errors', 0);
            ExceptionHandler::register($displayErrors);
        } elseif ($displayErrors && (! ini_get('log_errors') || ini_get('error_log'))) {
            // CLI - display errors only if they're not already logged to STDERR
            ini_set('display_errors', 1);
        }

        if ($displayErrors) {
            $handler = ErrorHandler::register(new ErrorHandler(new BufferingLogger()));
            $handler->throwAt(E_COMPILE_ERROR | E_CORE_ERROR | E_ERROR | E_PARSE, true);
        } else {
            $handler = ErrorHandler::register();
            $handler->throwAt(0, true);
        }

        app('container')->share(ErrorHandler::class, $handler);
//        DebugClassLoader::enable();
    }
}
