<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ComentarioController;
use App\Http\Controllers\TwoFactorController;


Route::redirect('/home', '/perfil');

Route::get('/', function () {
    return view('welcome');
});

Route::get('/prueba-xss', function () {
    return view('prueba-xss');
});

Route::post('/guardar-comentario', [ComentarioController::class, 'store']);

Route::get('/perfil', function () {
    $html = '<h1>Mi Perfil</h1>';

    // Botón original
    $html .= '<form method="POST" action="/2fa/enable">
                '.csrf_field().'
                <button type="submit">1. Activar / Regenerar 2FA</button>
              </form><br>
              <a href="/2fa/qr">Ver mi Código QR</a><br><br>';

    // NUEVO: Formulario para confirmar el código (Punto 3 de la práctica)
    $html .= '<form method="POST" action="/2fa/confirm">
                '.csrf_field().'
                <label>2. Ingresa el código de tu app para confirmar:</label><br>
                <input type="text" name="code" required maxlength="6">
                <button type="submit">Confirmar 2FA</button>
              </form><br><br>';
    // NUEVO: Formulario para deshabilitar (Punto 5)
    $html .= '<form method="POST" action="/2fa/disable">
                '.csrf_field().'
                <label>3. Deshabilitar 2FA (Ingresa código de recuperación):</label><br>
                <input type="text" name="recovery_code" required>
                <button type="submit">Apagar 2FA</button>
              </form><br><br>';    

    // Botón de logout
    $html .= '<form method="POST" action="/logout">
                '.csrf_field().'
                <button type="submit">Cerrar Sesión</button>
              </form>';

    return $html;
})->middleware('auth');

// 3. Lógica que valida tu primer código TOTP y activa el bloqueo
Route::post('/2fa/confirm', function(\Illuminate\Http\Request $request) {
    $user = auth()->user(); // <-- Extraemos al usuario directamente de la sesión
    $provider = app(\Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider::class);

    $valid = $provider->verify(decrypt($user->two_factor_secret), $request->code);

    if ($valid) {
        $user->forceFill(['two_factor_confirmed_at' => now()])->save();
        return '<h1>¡2FA Confirmado con éxito!</h1> <a href="/perfil">Volver</a>';
    }
    return '<h1>Código incorrecto, intenta de nuevo.</h1> <a href="/perfil">Volver</a>';
})->middleware('auth');

Route::post('/2fa/enable', [TwoFactorController::class, 'enable'])->middleware('auth');
Route::get('/2fa/qr', [TwoFactorController::class, 'qr'])->middleware('auth');

Route::post('/2fa/disable', function(\Illuminate\Http\Request $request) {
    $user = auth()->user();

    // Verificamos si el código enviado coincide con alguno de recuperación
    $recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);

    if (in_array($request->recovery_code, $recoveryCodes)) {
        // Deshabilitamos 2FA limpiando las columnas
        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        return '<h1>2FA Deshabilitado exitosamente</h1> <a href="/perfil">Volver al perfil</a>';
    }

    return '<h1>Código de recuperación inválido</h1> <a href="/perfil">Volver al perfil</a>';
})->middleware('auth');
