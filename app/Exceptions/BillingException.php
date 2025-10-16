<?php

namespace App\Exceptions;

use Exception;

class BillingException extends Exception
{
    public function __construct(string $message = 'Error en la facturación', int $code = 400, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
