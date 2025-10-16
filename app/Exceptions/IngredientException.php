<?php

namespace App\Exceptions;

use Exception;

class IngredientException extends Exception
{
    public static function insufficientStock(string $ingredientName, float $available, float $required): self
    {
        return new self(
            "Stock insuficiente para {$ingredientName}. Disponible: {$available}, Requerido: {$required}",
            400
        );
    }

    public static function notFound(int $ingredientId = null): self
    {
        $message = $ingredientId
            ? "Ingrediente #{$ingredientId} no encontrado"
            : "Ingrediente no encontrado";

        return new self($message, 404);
    }
}
