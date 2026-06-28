<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index() {
        return response()->json(Producto::all(), 200);
    }

    public function store(Request $request) {
        // Verificar ability 'crear' antes de ejecutar store()
        if (!$request->user()->tokenCan('crear')) { 
            abort(403, 'Acceso denegado: tu token no tiene permiso para crear.'); 
        }

        $data = $request->validate([
            'nombre'  => 'required|string|max:150',
            'precio'  => 'required|numeric|min:0',
            'stock'   => 'integer|min:0',
        ]);
        $producto = Producto::create($data);
        return response()->json($producto, 201);
    }

    public function show(Producto $producto) {
        return response()->json($producto);
    }

    public function update(Request $request, Producto $producto) {
        // Verificar ability 'editar' antes de ejecutar update()
        if (!$request->user()->tokenCan('editar')) { 
            abort(403, 'Acceso denegado: tu token no tiene permiso para editar.'); 
        }

        $producto->update($request->all());
        return response()->json($producto);
    }

    public function destroy(Request $request, Producto $producto) {
        // Verificar ability 'eliminar' antes de ejecutar destroy()
        if (!$request->user()->tokenCan('eliminar')) { 
            abort(403, 'Acceso denegado: tu token no tiene permiso para eliminar.'); 
        }

        $producto->delete();
        return response()->json(['mensaje' => 'Eliminado'], 200);
    }
}
