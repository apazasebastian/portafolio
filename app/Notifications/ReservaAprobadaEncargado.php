<?php

namespace App\Notifications;

use App\Models\Reserva;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReservaAprobadaEncargado extends Notification
{
    use Queueable;

    protected $reserva;

    /**
     * Create a new notification instance.
     */
    public function __construct(Reserva $reserva)
    {
        $this->reserva = $reserva;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $reserva = $this->reserva;
        $recinto = $reserva->recinto;
        
        return (new MailMessage)
            ->subject('Nueva Reserva Aprobada - ' . $recinto->nombre)
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line('Se ha aprobado una nueva reserva para el recinto' . $recinto->nombre . 'del cual eres encargado.')
            ->line('Detalles de la Reserva:')
            ->line('Organización: ' . $reserva->nombre_organizacion)
            ->line('Solicitante: ' . $reserva->nombre_solicitante)
            ->line('Correo: ' . $reserva->email_solicitante)
            ->line('Teléfono: ' . $reserva->telefono_solicitante)
            ->line('')
            ->line('Fecha:' . \Carbon\Carbon::parse($reserva->fecha_reserva)->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY'))
            ->line('Horario:' . substr($reserva->hora_inicio, 0, 5) . ' - ' . substr($reserva->hora_fin, 0, 5))
            ->line('Deporte/Actividad:' . ($reserva->deporte ?? 'No especificado'))
            ->line('Participantes:' . $reserva->numero_participantes . ' personas')
            ->line('')
            ->line('Descripción:')
            ->line($reserva->descripcion_actividad ?? 'Sin descripción')
            ->line('')
            ->action('Ver Reserva en el Sistema', url('/admin/reservas/' . $reserva->id))
            ->line('Por favor, asegúrate de que el recinto esté preparado para la fecha y hora indicadas.')
            ->salutation('Saludos, ' . config('app.name') . ' - Sistema de Reservas');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'reserva_id' => $this->reserva->id,
            'recinto_id' => $this->reserva->recinto_id,
            'fecha_reserva' => $this->reserva->fecha_reserva,
        ];
    }
}