<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\PedidoController;

// ==========================================
// VERSIÓN 1 (Estable - Nunca se rompe)
// ==========================================
Route::prefix('v1')->name('v1.')->group(function () {
    
    // Rutas Públicas v1
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    
    Route::get('/login', function () {
        return response()->json(['message' => 'No estas autenticado. Acceso denegado.'], 401);
    })->name('login');

    Route::apiResource('categorias', CategoriaController::class);
    Route::get('categorias/{categoria}/productos', [CategoriaController::class, 'productos']);
    Route::post('/categorias', [CategoriaController::class, 'store']);

    // Rutas Protegidas v1
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        
        Route::apiResource('productos', ProductoController::class);
        Route::apiResource('pedidos', PedidoController::class)->only(['store', 'show']);
    });
});

// ==========================================
// VERSIÓN 2 (Nuevas funcionalidades)
// ==========================================
Route::prefix('v2')->name('v2.')->group(function () {

    Route::post('/register', [\App\Http\Controllers\AuthController::class, 'register']);
    Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login']);
    Route::apiResource('categorias', \App\Http\Controllers\CategoriaController::class);

    Route::middleware('auth:sanctum')->group(function () {

        Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout']);
        Route::get('/me', [\App\Http\Controllers\AuthController::class, 'me']);

        Route::apiResource('productos', \App\Http\Controllers\Api\V2\ProductoController::class);
    });
});
