<?php

namespace App\Exceptions;

use Exception;

class TableException extends Exception
{
    public static function notAvailable(): self
    {
        return new self('La mesa no está disponible', 400);
    }

    public static function notFound(): self
    {
        return new self('Mesa no encontrada', 404);
    }

    public static function alreadyOccupied(): self
    {
        return new self('La mesa ya está ocupada', 400);
    }
}
