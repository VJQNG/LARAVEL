<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto; // <-- MUY IMPORTANTE: Importar el modelo

class ProductoController extends Controller
{
    // Búsqueda con múltiples parámetros del usuario [cite: 33-34]
    public function buscar(Request $request)
    {
        $query = Producto::query(); [cite: 36]

        if ($request->filled('nombre')) { [cite: 37]
            // LIKE también es seguro con Eloquent [cite: 38]
            $query->where('nombre', 'LIKE', '%' . $request->nombre . '%'); [cite: 39]
        }

        if ($request->filled('precio_max')) { [cite: 41]
            $query->where('precio', '<=', (float)$request->precio_max); [cite: 42]
        }

        if ($request->filled('categoria')) { [cite: 44]
            $query->whereIn('categoria_id', explode(',', $request->categoria)); [cite: 45]
        }

        return response()->json($query->paginate(15)); [cite: 47]
    }
}
