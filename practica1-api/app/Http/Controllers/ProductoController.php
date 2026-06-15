<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use App\Http\Resources\ProductoResource;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\StoreProductoRequest;
use App\Http\Requests\UpdateProductoRequest;
use OpenApi\Attributes as OA;

class ProductoController extends Controller
{
    #[OA\Get(
        path: "/api/v1/productos",
        summary: "Obtener lista paginada de productos",
        tags: ["Productos"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\Response(response: 200, description: "Devuelve la lista de productos disponibles")]
    public function index(Request $request)
    {
        // Acoplamos los scopes creados pasándole los parámetros de la petición de Vue
        $productos = Producto::with('categoria')
            ->buscar($request->busqueda)
            ->deCategoria($request->categoria_id)
            ->rangoPrecio($request->precio_min, $request->precio_max)
            ->orderBy($request->get('orden', 'nombre'), $request->get('dir', 'asc'))
            ->paginate($request->get('por_pagina', 15)); // Paginamos a 15 elementos por defecto

        // Retornamos la colección; Laravel inyectará los metadatos automáticamente
        return ProductoResource::collection($productos);
    }

    #[OA\Post(
        path: "/api/v1/productos",
        summary: "Crear un nuevo producto",
        tags: ["Productos"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\Parameter(
        name: "Accept",
        in: "header",
        required: true,
        description: "Forzar respuesta JSON",
        schema: new OA\Schema(type: "string", default: "application/json")
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["nombre", "precio", "stock", "categoria_id"/*, "imagen"*/],
            properties: [
                new OA\Property(property: "nombre", type: "string", example: "Teclado Mecánico Keychron"),
                new OA\Property(property: "descripcion", type: "string", example: "Teclado inalámbrico"),
                new OA\Property(property: "precio", type: "number", format: "float", example: 1500.50),
                new OA\Property(property: "stock", type: "integer", example: 100),
                // new OA\Property(property: "imagen", type: "string", example: "example.jpg"),
                new OA\Property(property: "categoria_id", type: "integer", example: 18)
            ]
        )
    )]
    #[OA\Response(response: 201, description: "Producto creado exitosamente")]
    #[OA\Response(response: 422, description: "Error de validación en los datos")]
    public function store(StoreProductoRequest $request)
    {
        Gate::authorize('create', Producto::class);
        /*$request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric',
            'stock' => 'integer',
            'imagen' => 'nullable|image|mimes:jpg,png,webp|max:2048',
            'categoria_id' => 'required|exists:categorias,id'
        ]);*/

        $data = $request->except('imagen');
        $data['descripcion'] = $request->descripcion ?? '';

        // Si el usuario adjuntó una imagen...
        if ($request->hasFile('imagen')) {
            // La guarda en disco y guarda la ruta (ej. 'productos/mifoto.jpg') en $data
            $data['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        $producto = Producto::create($data);
        
        return response()->json($producto, 201);
    }

    #[OA\Get(
        path: "/api/v1/productos/{producto}",
        summary: "Obtener los detalles de un producto específico",
        tags: ["Productos"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\Parameter(
        name: "producto",
        description: "ID numérico del producto",
        in: "path",
        required: true,
        schema: new OA\Schema(type: "integer")
    )]
    #[OA\Response(response: 200, description: "Detalles del producto obtenidos exitosamente")]
    #[OA\Response(response: 404, description: "Producto no encontrado en la base de datos")]
    public function show(Producto $producto)
    {
        return response()->json($producto, 200);
    }

    #[OA\Put(
        path: "/api/v1/productos/{producto}",
        summary: "Actualizar un producto existente",
        tags: ["Productos"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\Parameter(
        name: "producto",
        description: "ID del producto a modificar",
        in: "path",
        required: true,
        schema: new OA\Schema(type: "integer")
    )]
    #[OA\Parameter(
        name: "Accept",
        in: "header",
        required: true,
        schema: new OA\Schema(type: "string", default: "application/json")
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "nombre", type: "string", example: "Teclado Mecánico V2"),
                new OA\Property(property: "descripcion", type: "string", example: "Versión actualizada con switches blue"),
                new OA\Property(property: "precio", type: "number", format: "float", example: 1800.00),
                new OA\Property(property: "stock", type: "integer", example: 45),
                new OA\Property(property: "categoria_id", type: "integer", example: 18)
            ]
        )
    )]
    #[OA\Response(response: 200, description: "Producto actualizado correctamente")]
    #[OA\Response(response: 404, description: "Producto no encontrado")]
    #[OA\Response(response: 422, description: "Error de validación en los datos enviados")]
    public function update(UpdateProductoRequest $request, Producto $producto)
    {
        Gate::authorize('update', $producto);
        /*$request->validate([
            'nombre' => 'string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'numeric',
            'stock' => 'integer',
            'imagen' => 'nullable|image|mimes:jpg,png,webp|max:2048',
            'categoria_id' => 'required|exists:categorias,id'
        ]);*/

        $data = $request->except('imagen');
        $data['descripcion'] = $request->descripcion ?? '';

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        $producto->update($data);

        return response()->json($producto, 200);
    }

    #[OA\Delete(
        path: "/api/v1/productos/{producto}",
        summary: "Eliminar un producto del catálogo",
        tags: ["Productos"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\Parameter(
        name: "producto",
        description: "ID del producto a eliminar",
        in: "path",
        required: true,
        schema: new OA\Schema(type: "integer")
    )]
    #[OA\Response(response: 200, description: "Producto eliminado exitosamente")]
    #[OA\Response(response: 404, description: "Producto no encontrado")]
    public function destroy(Producto $producto)
    {
        Gate::authorize('delete', $producto);
        $producto->delete();
        
        return response()->json(null, 204);
    }
}
