<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; color: #333; background-color: #f4f4f4; padding: 20px; }
        .contenedor { background-color: #ffffff; padding: 30px; border-radius: 8px; max-width: 600px; margin: 0 auto; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .tabla { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .tabla td { padding: 15px 10px; border-bottom: 1px solid #eee; }
        .total-caja { background-color: #f8f9fa; padding: 15px; border-left: 4px solid #38bdf8; text-align: right; margin-top: 20px; }
        .img-prod { width: 60px; height: 60px; object-fit: cover; border-radius: 6px; border: 1px solid #ccc; }
    </style>
</head>
<body>
    <div class="contenedor">
        <h2 style="color: #1e1e1e;">¡Hola, {{ $usuario->name }}!</h2>
        <p>Recibimos tu pedido correctamente. Aquí tienes el resumen exacto de tu compra:</p>

        <table class="tabla">
            @foreach($pedido->items as $item)
            <tr>
                <td style="width: 70px;">
                    <img src="{{ $item->producto->imagen_url ?? 'https://placehold.co/50x50/2a2a2a/ffffff?text=X' }}" class="img-prod" alt="Producto">
                </td>
                <td>
                    <strong style="font-size: 16px;">{{ $item->producto->nombre }}</strong><br>
                    Cantidad: {{ $item->cantidad }} a ${{ number_format($item->precio_unitario, 2) }} c/u<br>
                    <strong style="color: #555;">Subtotal: ${{ number_format($item->precio_unitario * $item->cantidad, 2) }}</strong>
                </td>
            </tr>
            @endforeach
        </table>

        <div class="total-caja">
            <h3 style="margin: 0; color: #333;">Total General: <span style="color: #22c55e;">${{ number_format($pedido->total, 2) }}</span></h3>
        </div>
        <p style="margin-top: 20px; color: #666; font-size: 14px;">Gracias por tu preferencia. El equipo de Tienda Arch.</p>
    </div>
</body>
</html>
