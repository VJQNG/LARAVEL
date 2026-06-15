<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "Mi Tienda Arch API",
    description: "API REST para gestión de tienda Vue + Laravel"
)]
#[OA\Server(
    url: "http://localhost:8000",
    description: "Servidor Local"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    bearerFormat: "sanctum-token"
)]
#[OA\OpenApi(
    security: [["bearerAuth" => []]]
)]
class SwaggerConfig
{
    // Configuración global de Swagger
}
