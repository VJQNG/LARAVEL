<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    // Lista blanca de columnas permitidas (SELinux permisivo para estas)
    protected $fillable = [
        'user_id', 
        'total', 
        'estado', 
        'email_enviado_at'
    ];

    // Relación: Un pedido pertenece a un usuario (Para que el Job sepa a quién notificar)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación: Un pedido tiene muchos items (Para que el controlador pueda guardarlos)
    public function items()
    {
        return $this->hasMany(PedidoItem::class);
    }
}
