<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ... otras cosas que puedas tener aquí ...

        Gate::define('crear-producto', function (User $user) {
            return in_array($user->rol, ['admin', 'editor']);
        });

        Gate::define('editar-producto', function (User $user) {
            return in_array($user->rol, ['admin', 'editor']);
        });

        Gate::define('eliminar-producto', function (User $user) {
            // ¡Usamos el atajo que compilaste en el paso anterior!
            return $user->esAdmin();
        });
    }
}
