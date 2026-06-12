<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\Producto;
use App\Jobs\EnviarConfirmacionPedido;
use Illuminate\Support\Facades\DB;

class PedidoController extends Controller
{
    public function store(Request $request)
    {
        $pedido = DB::transaction(function () use ($request) {
            // 1. Crear la cabecera del pedido
            $p = Pedido::create([
                'user_id' => auth()->id(),
                'total' => collect($request->items)
                            ->sum(fn($i) => $i['precio_unitario'] * $i['cantidad']),
            ]);

            // 2. Guardar los detalles y descontar el inventario
            foreach ($request->items as $item) {
                $p->items()->create($item);
                Producto::find($item['producto_id'])
                        ->decrement('stock', $item['cantidad']);
            }

            return $p;
        });

        // 3. ¡El Disparador! Mandamos el trabajo al demonio en segundo plano (no bloquea la API)
        EnviarConfirmacionPedido::dispatch($pedido)->delay(now()->addSeconds(5));

        // 4. Respondemos al frontend inmediatamente con un 201 Created
        return response()->json(['pedido_id' => $pedido->id], 201);
    }
    
    // Aquí puedes agregar la función show() después si el manual lo pide para el polling
    public function show($id)
    {
        return response()->json(Pedido::findOrFail($id));
    }
}
