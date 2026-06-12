<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\PedidoController;


// Rutas Públicas (No requieren Token)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/login', function () {
    return response()->json(['message' => 'No estas autenticado. Acceso denegado.'], 401);
})->name('login');

Route::apiResource('categorias', CategoriaController::class);
Route::get('categorias/{categoria}/productos', [CategoriaController::class, 'productos']);
Route::post('/categorias', [CategoriaController::class, 'store']);

// Rutas Protegidas (Requieren Token Bearer en el Header)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Metemos el CRUD de productos dentro de la muralla de seguridad
    Route::apiResource('productos', ProductoController::class);
    # para el correo
    Route::post('/pedidos', [PedidoController::class, 'store']);
    Route::get('/pedidos/{id}', [PedidoController::class, 'show']);
});
