<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\Producto;
use App\Jobs\EnviarConfirmacionPedido;
use Illuminate\Support\Facades\DB;
// 1. Importamos las clases de los eventos
use App\Events\NuevoPedidoRecibido;
use App\Events\StockBajoAlerta;

class PedidoController extends Controller
{
    public function store(Request $request)
    {
        $pedido = DB::transaction(function () use ($request) {
            
            $p = Pedido::create([
                'user_id' => auth()->id(),
                'total' => collect($request->items)
                            ->sum(fn($i) => $i['precio_unitario'] * $i['cantidad']),
            ]);

            foreach ($request->items as $item) {
                $p->items()->create($item);
                
                // Buscamos el producto y descontamos el stock
                $producto = Producto::find($item['producto_id']);
                $producto->decrement('stock', $item['cantidad']);
                
                // 2. Disparador 1: Si el stock queda en 5 o menos, lanzamos la alerta
                if ($producto->stock <= 5) {
                    broadcast(new StockBajoAlerta($producto, $producto->stock));
                }
            }

            return $p;
        });

        // Este es el Job del correo de la práctica anterior
        EnviarConfirmacionPedido::dispatch($pedido)->delay(now()->addSeconds(5));

        // 3. Disparador 2: Avisamos por WebSockets que entró una nueva venta
        broadcast(new NuevoPedidoRecibido($pedido))->toOthers();

        return response()->json(['pedido_id' => $pedido->id], 201);
    }
    
    public function show($id)
    {
        return response()->json(Pedido::findOrFail($id));
    }
}
