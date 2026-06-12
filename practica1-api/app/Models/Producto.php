<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'descripcion', 'precio', 'stock', 'imagen', 'categoria_id'];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function scopeBuscar($query, $termino)
    {
        return $query->when($termino, function ($q) use ($termino) {
            $q->where(function ($sub) use ($termino) {
                $sub->where('nombre', 'LIKE', "%{$termino}%")
                    ->orWhere('descripcion', 'LIKE', "%{$termino}%")
                    ->orWhere('id', 'like', "%{$termino}%")
                    ->orWhere('precio', 'like', "%{$termino}%")
                    ->orWhere('stock', 'like', "%{$termino}%");
            });
        });
    }

    /**
     * Scope para filtrar por categoría si se proporciona el ID.
     */
    public function scopeDeCategoria($query, $categoriaId)
    {
        return $query->when($categoriaId, function ($q) use ($categoriaId) {
            $q->where('categoria_id', $categoriaId);
        });
    }

    /**
     * Scope para filtrar por un rango dinámico de precios.
     */
    public function scopeRangoPrecio($query, $min, $max)
    {
        return $query
            ->when($min, function ($q) use ($min) {
                $q->where('precio', '>=', $min);
            })
            ->when($max, function ($q) use ($max) {
                $q->where('precio', '<=', $max);
            });
    }
}
