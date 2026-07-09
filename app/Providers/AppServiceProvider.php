<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Fortify\Fortify;
use Illuminate\Support\Facades\RateLimiter; // <-- Para el límite de intentos
use Illuminate\Cache\RateLimiting\Limit;      // <-- Para definir el límite
use Illuminate\Http\Request;                  // <-- Para identificar la IP

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // Auditoría de consultas SQL (Práctica 5)
        if (config('app.debug')) {
            DB::listen(function ($query) {
                Log::channel('daily')->info('SQL Query', [
                    'sql'      => $query->sql,
                    'bindings' => $query->bindings,
                    'time'     => $query->time . 'ms',
                ]);
            });
        }

        // Regla de seguridad de Fortify para evitar fuerza bruta en el Login
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        // Configuración de vistas de Fortify
        Fortify::twoFactorChallengeView(function () {
            return view('auth.two-factor-challenge');
        });

        Fortify::loginView(function () {
            return view('auth.login');
        });
        
        Fortify::registerView(function () {
            return view('auth.register');
        });
    }
}
