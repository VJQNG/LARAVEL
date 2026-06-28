<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CategoriaController;
use Illuminate\Support\Facades\Route;

// Rutas públicas
Route::post('/login', [AuthController::class, 'login']);

// Rutas protegidas con Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/perfil', [AuthController::class, 'perfil']);
    
    // Tus recursos de las prácticas anteriores ahora son privados
    Route::apiResource('productos', ProductoController::class);
    Route::apiResource('categorias', CategoriaController::class);
});
