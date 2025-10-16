<?php

namespace App\Exceptions;

use Exception;

class CategoryException extends Exception
{
    public static function hasProducts(string $categoryName = null): self
    {
        $message = $categoryName
            ? "No se puede eliminar la categoría '{$categoryName}' porque tiene productos asociados"
            : "No se puede eliminar la categoría porque tiene productos asociados";

        return new self($message, 400);
    }

    public static function notFound(int $categoryId = null): self
    {
        $message = $categoryId
            ? "Categoría #{$categoryId} no encontrada"
            : "Categoría no encontrada";

        return new self($message, 404);
    }
}
