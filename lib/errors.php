<?php

declare(strict_types=1);

use NGSOFT\Facades\Logger,
    Psr\Log\LogLevel;

/**
 * Prevents displaying warnings and stop script execution
 * just set error_reporting() to a specific value to prevent some errors
 */
if ( ! class_exists(WarningException::class)) {

    class WarningException extends ErrorException
    {

    }

}
if ( ! class_exists(ParseException::class)) {

    class ParseException extends ErrorException
    {

    }

}
if ( ! class_exists(NoticeException::class)) {

    class NoticeException extends ErrorException
    {

    }

}
if ( ! class_exists(CoreErrorException::class)) {

    class CoreErrorException extends ErrorException
    {

    }

}
if ( ! class_exists(CoreWarningException::class)) {

    class CoreWarningException extends ErrorException
    {

    }

}
if ( ! class_exists(CompileErrorException::class)) {

    class CompileErrorException extends ErrorException
    {

    }

}
if ( ! class_exists(CompileWarningException::class)) {

    class CompileWarningException extends ErrorException
    {

    }

}
if ( ! class_exists(UserErrorException::class)) {

    class UserErrorException extends ErrorException
    {

    }

}
if ( ! class_exists(UserWarningException::class)) {

    class UserWarningException extends ErrorException
    {

    }

}
if ( ! class_exists(UserNoticeException::class)) {

    class UserNoticeException extends ErrorException
    {

    }

}
if ( ! class_exists(StrictException::class)) {

    class StrictException extends ErrorException
    {

    }

}
if ( ! class_exists(RecoverableErrorException::class)) {

    class RecoverableErrorException extends ErrorException
    {

    }

}
if ( ! class_exists(DeprecatedException::class)) {

    class DeprecatedException extends ErrorException
    {

    }

}
if ( ! class_exists(UserDeprecatedException::class)) {

    class UserDeprecatedException extends ErrorException
    {

    }

}

if ( ! function_exists('list_error_handlers')) {

    function list_error_handlers(): array
    {
        $result = [];

        // unset/get the handlers
        while ($handler = get_error_handler()) {
            $result[] = $handler;
            restore_error_handler();
        }
        // reset the handlers
        $stack = $result;
        while ($handler = array_pop($stack)) {
            set_error_handler($handler);
        }
        return $result;
    }

}


if ( ! function_exists('get_error_handler')) {

    /**
     * Get the current set error handler
     *
     * @phan-suppress PhanTypeMismatchArgumentInternal
     */
    function get_error_handler(): ?callable
    {

        try {
            return set_error_handler(fn() => null);
        } finally {
            restore_error_handler();
        }
    }

}


if ( ! function_exists('set_default_error_handler')) {

    /**
     * Set error handler to throw targeted exceptions
     */
    function set_default_error_handler(bool $log = false): ?callable
    {
        static $handler, $errors = [
            E_ERROR => ErrorException::class,
            E_WARNING => WarningException::class,
            E_PARSE => ParseException::class,
            E_NOTICE => NoticeException::class,
            E_CORE_ERROR => CoreErrorException::class,
            E_CORE_WARNING => CoreWarningException::class,
            E_COMPILE_ERROR => CompileErrorException::class,
            E_COMPILE_WARNING => CoreWarningException::class,
            E_USER_ERROR => UserErrorException::class,
            E_USER_WARNING => UserWarningException::class,
            E_USER_NOTICE => UserNoticeException::class,
            E_STRICT => StrictException::class,
            E_RECOVERABLE_ERROR => RecoverableErrorException::class,
            E_DEPRECATED => DeprecatedException::class,
            E_USER_DEPRECATED => UserDeprecatedException::class],
        $levels = [
            E_ERROR => LogLevel::ERROR,
            E_WARNING => LogLevel::WARNING,
            E_PARSE => LogLevel::CRITICAL,
            E_NOTICE => LogLevel::NOTICE,
            E_CORE_ERROR => LogLevel::ERROR,
            E_CORE_WARNING => LogLevel::WARNING,
            E_COMPILE_ERROR => LogLevel::EMERGENCY,
            E_COMPILE_WARNING => LogLevel::CRITICAL,
            E_USER_ERROR => LogLevel::ERROR,
            E_USER_WARNING => LogLevel::WARNING,
            E_USER_NOTICE => LogLevel::NOTICE,
            E_STRICT => LogLevel::NOTICE,
            E_RECOVERABLE_ERROR => LogLevel::WARNING,
            E_DEPRECATED => LogLevel::NOTICE,
            E_USER_DEPRECATED => LogLevel::NOTICE,
        ];

        if ( ! $handler) {
            $handler = static function (
                    int $errno,
                    string $errstr,
                    string $errfile,
                    int $errline
            )use ($errors, $levels, $log): bool {

                if ( ! (error_reporting() & $errno)) {
                    return false;
                }
                if ($class = $errors[$errno] ?? null) {


                    $log && Logger::log($levels[$errno], $errstr, [
                                'line' => $errline,
                                'file' => $errfile
                    ]);

                    throw new $class($errstr, 0, $errno, $errfile, $errline);
                }
                return true;
            };
        }


        //prevent setting multiple times
        if (get_error_handler() === $handler) {
            return $handler;
        }

        return set_error_handler($handler);
    }

}


