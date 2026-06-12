<?php

namespace App\Notifications;

use App\Models\Pedido;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString; // <-- Importación crucial para inyectar HTML

class ConfirmacionPedidoNotification extends Notification
{
    use Queueable;

    public function __construct(public Pedido $pedido)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Confirmación de tu pedido #' . $this->pedido->id)
            ->view('emails.pedido', [
                'pedido' => $this->pedido,
                'usuario' => $notifiable
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
