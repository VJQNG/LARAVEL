<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SoloCelular
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Revisar el User-Agent de la petición
        $userAgent = $request->userAgent();

        // Detectar si proviene de un dispositivo móvil
        if (preg_match('/Mobile|Android|iPhone|iPad|iPod/i', $userAgent)) {
            // Redirigir a la ruta '/movil' si es así
            return redirect('/movil');
        }

        // Si es de escritorio, dejamos pasar la petición
        return $next($request);
    }
}
