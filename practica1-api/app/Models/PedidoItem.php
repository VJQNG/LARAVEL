<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidoItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'pedido_id', 
        'producto_id', 
        'cantidad', 
        'precio_unitario'
    ];

    // Relación: Este item pertenece a un pedido
    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    // Relación: Este item enlaza a un producto del catálogo
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
