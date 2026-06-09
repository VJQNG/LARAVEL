<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use App\Http\Resources\ProductoResource;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ProductoResource::collection(Producto::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric',
            'stock' => 'integer',
            'imagen' => 'nullable|image|mimes:jpg,png,webp|max:2048',
            'categoria_id' => 'required|exists:categorias,id'
        ]);

        $data = $request->except('imagen');

        // Si el usuario adjuntó una imagen...
        if ($request->hasFile('imagen')) {
            // La guarda en disco y guarda la ruta (ej. 'productos/mifoto.jpg') en $data
            $data['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        $producto = Producto::create($data);
        
        return response()->json($producto, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Producto $producto)
    {
        return response()->json($producto, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Producto $producto)
    {
        $request->validate([
            'nombre' => 'string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'numeric',
            'stock' => 'integer',
            'imagen' => 'nullable|image|mimes:jpg,png,webp|max:2048',
            'categoria_id' => 'required|exists:categorias,id'
        ]);

        $data = $request->except('imagen');

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        $producto->update($data);

        return response()->json($producto, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Producto $producto)
    {
        $producto->delete();
        
        return response()->json(null, 204);
    }
}
