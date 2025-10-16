<?php

namespace App\Exceptions;

use Exception;

class OrderException extends Exception
{
    public static function notFound(): self
    {
        return new self('Pedido no encontrado', 404);
    }

    public static function notEditable(): self
    {
        return new self('No tienes permisos para editar este pedido', 403);
    }

    public static function invalidStatus(string $status): self
    {
        return new self("Estado no válido: {$status}", 400);
    }

    public static function cannotCombine(int $orderId, string $status): self
    {
        return new self("El pedido #{$orderId} ya está {$status}", 400);
    }

    public static function noOrdersToCombin(): self
    {
        return new self('No se encontraron pedidos para combinar', 404);
    }

    public static function cannotComplete(string $productName, string $reason): self
    {
        return new self("No se puede completar la orden. Faltan ingredientes para {$productName}: {$reason}", 400);
    }
}
