<?php

declare(strict_types=1);

namespace NGSOFT\Exceptions;

use NGSOFT\RegExp,
    RuntimeException,
    Throwable;

class RegExpException extends RuntimeException {

    public function __construct(
            protected RegExp $regExp,
            string $message = "",
            int $code = 0,
            Throwable $previous = null
    ) {
        $code = $code !== 0 ? $code : preg_last_error();
        if (empty($message)) $message = sprintf('Regex "%s", %s', $regExp, preg_last_error_msg());

        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the RegExp Object
     * @return RegExp
     */
    public function getRegExp(): RegExp {
        return $this->regExp;
    }

}
