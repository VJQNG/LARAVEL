<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Http\Resources\CategoriaResource;
use App\Http\Resources\ProductoResource;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return CategoriaResource::collection(
            Categoria::with('productos')->get()
        );
    }

    public function productos(Categoria $categoria)
    {
        return ProductoResource::collection(
            $categoria->productos()->with('categoria')->get()
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Categoria $categoria)
    {
        //
    }

    /**
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    //}
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    //}
     * Update the specified resource in storage.
     */
    /**
     * Actualizar una categoría existente.
     */
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

        return response()->json([
            'message' => 'Categoría actualizada exitosamente',
            'data'    => $categoria
        ], 200);
    }

    /**
     * Eliminar una categoría.
     */
    public function destroy(Categoria $categoria)
    {
        if ($categoria->productos()->count() > 0) {
            return response()->json([
                'message' => 'No se puede eliminar: Esta categoría está en uso por algunos productos.'
            ], 409); // El código 409 significa 'Conflicto'
        }

        // Opcional: Podrías verificar si tiene productos antes de borrarla
        $categoria->delete();

        return response()->json([
            'message' => 'Categoría eliminada correctamente'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */

    public function store(Request $request)
    {
        $request->validate([
            'nombre'      => 'required|string|max:255'
        ]);

        $categoria = Categoria::create([
            'nombre'      => $request->nombre,
            'descripcion' => $request->descripcion
        ]);

        return response()->json([
            'message' => 'Categoría creada',
            'data'    => $categoria
        ], 201);
    }
}
