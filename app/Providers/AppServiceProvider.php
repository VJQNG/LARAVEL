<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Fortify\Fortify;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;

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


        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by(
                $request->input('email') . '|' . $request->ip()
            )->response(function () {
                return response()->json([
                    'mensaje' => 'Demasiados intentos. Espera 1 minuto.',
                    'retry_after' => 60
                ], 429);
            });
        });

        RateLimiter::for('api', function (Request $request) {
            return $request->user()
                ? Limit::perMinute(60)->by($request->user()->id)
                : Limit::perMinute(10)->by($request->ip());
        });

        RateLimiter::for('sensible', function (Request $request) {
            return Limit::perHour(3)->by($request->ip());
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
