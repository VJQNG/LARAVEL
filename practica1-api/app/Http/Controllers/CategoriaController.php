<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use App\Models\Categoria;
use App\Http\Resources\CategoriaResource;
use App\Http\Resources\ProductoResource;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class CategoriaController extends Controller
{
    #[OA\Get(
        path: "/api/v1/categorias",
        summary: "Obtener lista de categorías",
        tags: ["Categorías"]
    )]
    #[OA\Response(
        response: 200,
        description: "Devuelve la lista de categorías disponibles"
    )]
    public function index()
    {
        // 1. Buscamos en la caché 'categorias.todas'. Si no existe, hace la consulta y la guarda 1 hora (3600s)
        $categorias = Cache::remember('categorias.todas', 3600, function () {
            return CategoriaResource::collection(
                Categoria::with('productos')->get()
            )->toArray(request());
        });

        return response()->json(['data' => $categorias]);
    }

    public function productos(Categoria $categoria)
    {
        return ProductoResource::collection(
            $categoria->productos()->with('categoria')->get()
        );
    }

    public function show(Categoria $categoria)
    {
        //
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'      => 'required|string|max:255'
        ]);

        $categoria = Categoria::create([
            'nombre'      => $request->nombre,
            'descripcion' => $request->descripcion
        ]);

        // 2. Destruimos la caché porque hay una categoría nueva
        Cache::forget('categorias.todas');

        return response()->json([
            'message' => 'Categoría creada',
            'data'    => $categoria
        ], 201);
    }

    public function update(Request $request, Categoria $categoria)
    {
        $request->validate([
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string'
        ]);

        $categoria->update([
            'nombre'      => $request->nombre,
            'descripcion' => $request->descripcion
        ]);

        // 2. Destruimos la caché porque la categoría cambió
        Cache::forget('categorias.todas');

        return response()->json([
            'message' => 'Categoría actualizada exitosamente',
            'data'    => $categoria
        ], 200);
    }

    public function destroy(Categoria $categoria)
    {
        if ($categoria->productos()->count() > 0) {
            return response()->json([
                'message' => 'No se puede eliminar: Esta categoría está en uso por algunos productos.'
            ], 409); // El código 409 significa 'Conflicto'
        }

        $categoria->delete();

        // 2. Destruimos la caché porque eliminamos una categoría
        Cache::forget('categorias.todas');

        return response()->json([
            'message' => 'Categoría eliminada correctamente'
        ], 200);
    }
}
