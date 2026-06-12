<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;
    // 1. Especificamos el nombre exacto de la tabla por buenas prácticas
    protected $table = 'categorias';

    // 2. Definimos los campos que Vue tiene permitido enviarnos
    protected $fillable = ['nombre', 'descripcion'];

    // 3. Declaramos la relación: Una categoría tiene muchos productos
    public function productos()
    {
        return $this->hasMany(Producto::class);
    }
}
