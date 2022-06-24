<?php

declare(strict_types=1);

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


if ( ! function_exists('handle_errors')) {




    /**
     * Set error handler to throw targeted exceptions
     */
    function set_default_error_handler(): ?callable
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
            E_USER_DEPRECATED => UserDeprecatedException::class,
        ];

        if ( ! $handler) {
            $handler = static function (
                    int $errno,
                    string $errstr,
                    string $errfile,
                    int $errline
            )use ($errors): bool {

                if ( ! (error_reporting() & $errno)) {
                    return false;
                }
                if ($class = $errors[$errno] ?? null) {
                    throw new $class($errstr, 0, $errno, $errfile, $errline);
                }
                return true;
            };
        }

        if (get_error_handler() === $handler) {

            return $handler;
        }

        return set_error_handler($handler);
    }

}