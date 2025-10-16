<?php

namespace App\Exceptions;

use Exception;

class ValidationException extends Exception
{
    public static function orderCannotBeModified(string $status): self
    {
        return new self("El pedido no se puede modificar en su estado actual: {$status}", 400);
    }

    public static function invalidData(string $field, string $reason = ''): self
    {
        $message = "Datos inválidos en el campo: {$field}";
        if ($reason) {
            $message .= ". {$reason}";
        }
        return new self($message, 422);
    }

    public static function missingRequired(string $field): self
    {
        return new self("El campo {$field} es obligatorio", 422);
    }
}
