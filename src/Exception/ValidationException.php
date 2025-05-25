<?php

namespace Validator\Exception;

use Exception;

class ValidationException extends Exception
{
    public const UNKNOWN_ERROR = 100;
    public const INVALID_RULE = 101;

    public function __construct($message = '', $code = 0, ?Exception $previous = null)
    {
        $code = $code === 0 ? 100 : $code;
        parent::__construct($message, $code, $previous);
    }
}
