<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function index()
    {
        // GET /api/categorias -> 200 OK 
        return response()->json(Categoria::all(), 200); 
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:150', // 
            'slug'   => 'required|string|unique:categorias,slug', // 
        ]);
        
        $categoria = Categoria::create($data);
        
        // POST /api/categorias -> 201 Created 
        return response()->json($categoria, 201); 
    }

    public function show(Categoria $categoria)
    {
        // GET /api/categorias/{id} -> 200 OK 
        return response()->json($categoria, 200); 
    }

    public function update(Request $request, Categoria $categoria)
    {
        $data = $request->validate([
            'nombre' => 'sometimes|required|string|max:150',
            'slug'   => 'sometimes|required|string|unique:categorias,slug,' . $categoria->id,
        ]);

        $categoria->update($data);
        
        // PUT/PATCH /api/categorias/{id} -> 200 OK 
        return response()->json($categoria, 200); 
    }

    public function destroy(Categoria $categoria)
    {
        $categoria->delete();
        
        // DELETE /api/categorias/{id} -> 200 OK 
        return response()->json(['mensaje' => 'Categoría eliminada'], 200); 
    }
}
