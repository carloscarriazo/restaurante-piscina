<?php

namespace App\Exceptions;

use Exception;

class OrderCreationException extends Exception
{
    public function __construct(string $message = 'Error al crear la orden', int $code = 500, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
