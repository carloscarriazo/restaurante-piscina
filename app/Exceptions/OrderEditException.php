<?php

namespace App\Exceptions;

use Exception;

class OrderEditException extends Exception
{
    public function __construct(string $message = 'Error al editar la orden', int $code = 400, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
