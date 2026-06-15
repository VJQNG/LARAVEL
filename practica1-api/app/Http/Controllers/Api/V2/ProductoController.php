<?php

namespace App\Http\Controllers\Api\V2;

// Importamos el controlador v1 original para heredar sus funciones
use App\Http\Controllers\ProductoController as V1ProductoController;
use App\Models\Producto;
use App\Http\Resources\ProductoResource;
use Illuminate\Http\Request;

class ProductoController extends V1ProductoController
{
    public function index(Request $request)
    {
        // v2: agrega búsqueda full-text con MySQL FULLTEXT
        $query = Producto::with('categoria');

        if ($request->categoria_id) {
            $query->where('categoria_id', $request->categoria_id);
        }

        if ($request->busqueda) {
            $busqueda = $request->busqueda;
            $termino = $busqueda . '*';

            $query->where(function ($q) use ($busqueda, $termino) {

                // Búsqueda inteligente en nombre y descripción
                $q->whereFullText(['nombre', 'descripcion'], $termino, ['mode' => 'boolean']);

                // Si el usuario tecleó un número, también buscamos coincidencias exactas o parciales en las otras columnas
                if (is_numeric($busqueda)) {
                    $q->orWhere('id', $busqueda)
                      ->orWhere('precio', 'LIKE', "%{$busqueda}%")
                      ->orWhere('stock', $busqueda);
                }
            });
        }

        return ProductoResource::collection(
            $query->paginate(20)
        );
    }
}
