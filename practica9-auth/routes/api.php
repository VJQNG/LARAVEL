<?php

use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CategoriaController;
use Illuminate\Support\Facades\Route;

Route::apiResource('productos', ProductoController::class);
Route::apiResource('categorias', CategoriaController::class);
