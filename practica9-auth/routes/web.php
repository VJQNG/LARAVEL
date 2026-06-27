<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ArticuloController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Ruta de perfil de usuario
Route::middleware('auth')->group(function () {
    Route::get('/perfil', function () {
        return view('perfil', ['user' => auth()->user()]);
    })->name('perfil');
});

Route::get('/admin/panel', function () {
    return view('admin.panel');
})->middleware('verificar.rol:admin')->name('admin.panel');

Route::middleware(['auth', 'verificar.rol:editor'])->group(function () {
    Route::get('/editor/articulos', [ArticuloController::class, 'index']);
    Route::post('/editor/articulos', [ArticuloController::class, 'store']);
});

Route::get('/movil', function () {
    return 'Estás navegando desde un dispositivo móvil.';
});

// Aplicar el middleware a al menos 3 rutas
Route::middleware('solo.celular')->group(function () {
    Route::get('/noticias', function () { return 'Noticias (Versión de Escritorio)'; });
    Route::get('/contacto', function () { return 'Contacto (Versión de Escritorio)'; });
    Route::get('/acerca-de', function () { return 'Acerca de (Versión de Escritorio)'; });
});

require __DIR__.'/auth.php';
