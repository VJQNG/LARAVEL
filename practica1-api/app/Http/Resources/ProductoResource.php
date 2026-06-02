<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'precio' => $this->precio,
            'stock' => $this->stock,
            // Aquí está la magia: si hay imagen, genera la URL completa; si no, manda null
            'imagen_url' => $this->imagen ? asset('storage/' . $this->imagen) : null,
            'created_at' => $this->created_at,
        ];
    }
}
