<?php

namespace App\Constants;

/**
 * Constantes de mensajes reutilizables
 */
class MessageConstants
{
    // Mensajes de error
    public const PRODUCT_NOT_FOUND = 'Producto no encontrado.';
    public const ORDER_NOT_FOUND = 'Pedido no encontrado';
    public const USER_NOT_FOUND = 'Usuario no encontrado';
    public const INGREDIENT_NOT_FOUND = 'Ingrediente no encontrado';
    public const RECIPE_NOT_FOUND = 'Receta no encontrada';
    public const CATEGORY_NOT_FOUND = 'Categoría no encontrada';
    public const INSUFFICIENT_STOCK = 'Stock insuficiente';
    public const CANNOT_DELETE_CATEGORY_WITH_PRODUCTS = 'No se puede eliminar la categoría porque tiene productos asociados.';

    // Mensajes de éxito
    public const ORDER_READY = '¡Pedido Listo!';

    // Tipos de bebidas
    public const CLUB_COLOMBIA = 'Club Colombia';

    // Mensajes de validación
    public const PASSWORD_MISMATCH = 'Las contraseñas no coinciden';
}
