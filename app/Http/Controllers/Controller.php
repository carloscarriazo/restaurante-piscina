<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="API Restaurante Piscina",
 *     version="1.0.0",
 *     description="API para gestión de restaurante con módulos de cocina, meseros, órdenes, facturación y más",
 *     @OA\Contact(
 *         email="soporte@restaurante-piscina.com",
 *         name="Soporte Técnico"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="API Server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Ingrese el token JWT obtenido del login"
 * )
 *
 * @OA\Tag(
 *     name="Autenticación",
 *     description="Endpoints para autenticación de usuarios"
 * )
 *
 * @OA\Tag(
 *     name="Cocina",
 *     description="Endpoints para el módulo de cocina"
 * )
 *
 * @OA\Tag(
 *     name="Meseros",
 *     description="Endpoints para el módulo de meseros"
 * )
 *
 * @OA\Tag(
 *     name="Órdenes",
 *     description="Gestión de órdenes"
 * )
 *
 * @OA\Tag(
 *     name="Productos",
 *     description="Gestión de productos del menú"
 * )
 *
 * @OA\Tag(
 *     name="Mesas",
 *     description="Gestión de mesas"
 * )
 *
 * @OA\Tag(
 *     name="Facturación",
 *     description="Procesamiento de pagos y facturación"
 * )
 *
 * @OA\Tag(
 *     name="Reportes",
 *     description="Generación de reportes y estadísticas"
 * )
 */
abstract class Controller
{
    //
}
