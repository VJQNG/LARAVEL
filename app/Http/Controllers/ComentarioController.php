<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ComentarioController extends Controller
{
    public function store(Request $request)
    {
	//dd($request->all());
        // 1. Validación estricta
        $validated = $request->validate([
            'titulo'     => 'required|string|max:200|regex:/^[a-zA-Z0-9 .,!?-]+$/',
            'contenido'  => 'required|string|max:5000',
            'email'      => 'required|email:rfc,dns',
        ]);

        // 2. Sanitización adicional (neutraliza scripts)
        $contenido_limpio = strip_tags($validated['contenido']);

        // Retornamos a la vista con el payload inyectado (para probar el XSS)
        return view('prueba-xss', [
            'titulo' => $validated['titulo'],
            'comentario_crudo' => $request->contenido,
            'comentario_limpio' => $contenido_limpio
        ]);
    }
}
