<?php

namespace App\Exceptions;

use Exception;

class PaymentException extends Exception
{
    public function __construct(string $message = 'Error al procesar el pago', int $code = 500, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
