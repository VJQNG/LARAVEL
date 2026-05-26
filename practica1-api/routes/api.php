<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController;

// Esto genera los endpoints: /api/productos
Route::apiResource('productos', ProductoController::class);
