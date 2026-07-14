<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Services\AuditoriaService;

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


// 1. Simular LOGIN FALLIDO y LOGIN EXITOSO
Route::post('/login', function (Request $request) {
    if ($request->email === 'admin@example.com' && $request->password === 'secret') {
        AuditoriaService::log('LOGIN_EXITOSO', ['email' => $request->email]);
        return response()->json(['token' => 'token_generado_123']);
    }

    AuditoriaService::security('LOGIN_FALLIDO', 'warning', ['email_intento' => $request->email]);
    return response()->json(['error' => 'Credenciales incorrectas'], 401);
});

// 2. Simular PRODUCTO ELIMINADO
Route::delete('/productos/{id}', function ($id) {
    AuditoriaService::log('PRODUCTO_ELIMINADO', ['producto_id' => $id, 'producto_nombre' => 'Laptop_Gamer']);
    return response()->json(['mensaje' => 'Eliminado']);
});

// 3. Simular CAMBIO DE CONTRASEÑA
Route::post('/password/reset', function (Request $request) {
    AuditoriaService::log('CAMBIO_CONTRASEÑA', ['email' => $request->email]);
    return response()->json(['mensaje' => 'Contraseña actualizada']);
});

// 4. Simular ACCESO DENEGADO
Route::get('/admin/secret', function () {
    AuditoriaService::security('ACCESO_DENEGADO', 'critical', ['recurso' => '/admin/secret']);
    return response()->json(['error' => 'No tienes permisos'], 403);
});
