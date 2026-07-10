<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Límite API general (10 peticiones por IP)
Route::middleware('throttle:api')->get('/productos', function () {
    return response()->json(['mensaje' => 'Lista de productos']);
});

// Límite súper estricto (3 por hora)
Route::middleware('throttle:sensible')->post('/password/reset', function () {
    return response()->json(['mensaje' => 'Recuperación']);
});

// Límite de Login (5 por minuto)
Route::middleware('throttle:login')->post('/login', function () {
    return response()->json(['error' => 'Credenciales incorrectas'], 401);
});
