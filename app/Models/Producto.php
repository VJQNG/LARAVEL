<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    // ✅ Especificar SOLO los campos asignables masivamente [cite: 63-64]
    protected $fillable = ['nombre', 'descripcion', 'precio', 'stock'];

    // Ocultamos información sensible o de sistema al devolver JSON [cite: 67]
    protected $hidden = ['deleted_at'];

    // Forzamos el tipo de dato al salir o entrar a la base de datos [cite: 68-71]
    protected $casts = [
        'precio' => 'decimal:2',
        'activo' => 'boolean',
    ];
}
