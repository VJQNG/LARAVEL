<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\User;

Broadcast::routes(['middleware' => ['auth:sanctum']]);

// Definimos que para entrar al canal 'admin-panel', el usuario debe pasar esta prueba:
Broadcast::channel('admin-panel', function (User $user) {
    return $user->esAdmin(); // Retorna true si es administrador, false si no lo es
});
