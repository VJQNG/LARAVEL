<?php

namespace App\Jobs;

use App\Models\Pedido;
use App\Notifications\ConfirmacionPedidoNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class EnviarConfirmacionPedido implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Configuración de tolerancia a fallos
    public int $tries = 3;
    public int $timeout = 60;

    // Recibimos el pedido al instanciar el Job
    public function __construct(public Pedido $pedido)
    {
    }

    public function handle(): void
    {
        // Enviar la notificación al usuario
        $this->pedido->user->notify(
            new ConfirmacionPedidoNotification($this->pedido)
        );

        // Marcar como enviado en la base de datos
        $this->pedido->update([
            'email_enviado_at' => now()->toDateTimeString()
        ]);
    }

    public function failed(Throwable $e): void
    {
        // Si falla después de los 3 intentos, lo registramos en el journal/log
        Log::error('Fallo envío email pedido ' . $this->pedido->id);
    }
}
