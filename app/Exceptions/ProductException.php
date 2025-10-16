<?php

namespace App\Exceptions;

use Exception;

class ProductException extends Exception
{
    public static function notFound(): self
    {
        return new self('Producto no encontrado', 404);
    }

    public static function notAvailable(): self
    {
        return new self('El producto no está disponible', 400);
    }

    public static function cannotDelete(): self
    {
        return new self('No se puede eliminar el producto porque tiene órdenes activas', 400);
    }

    public static function insufficientStock(string $productName): self
    {
        return new self("Stock insuficiente para el producto: {$productName}", 400);
    }
}
