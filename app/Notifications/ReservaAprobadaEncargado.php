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
     * creacion de la reserva
     */
    public function __construct(Reserva $reserva)
    {
        $this->reserva = $reserva;
    }

    /**
     * transmisor
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Esta bien asi
     */
    public function toMail(object $notifiable): MailMessage
    {
        $reserva = $this->reserva;
        $recinto = $reserva->recinto;
        
        return (new MailMessage)
            ->subject('Nueva Reserva Aprobada - ' . $recinto->nombre)
            ->greeting('Estimado/a ' . $notifiable->name . ',')
            ->line('Se ha aprobado una nueva reserva para el recinto ' . $recinto->nombre . ' del cual usted es encargado.')
            ->line('')
            ->line('DETALLES DE LA RESERVA:')
            ->line('')
            ->line('Organización: ' . $reserva->nombre_organizacion)
            ->line('Solicitante: ' . $reserva->representante_nombre)
            ->line('Correo electrónico: ' . $reserva->email)
            ->line('Teléfono: ' . $reserva->telefono)
            ->line('')
            ->line('Fecha: ' . \Carbon\Carbon::parse($reserva->fecha_reserva)->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY'))
            ->line('Horario: ' . substr($reserva->hora_inicio, 0, 5) . ' - ' . substr($reserva->hora_fin, 0, 5) . ' hrs.')
            ->line('Deporte/Actividad: ' . ($reserva->deporte ?? 'No especificado'))
            ->line('Número de participantes: ' . $reserva->cantidad_personas . ' personas')
            ->line('')
            ->line('Observaciones: ' . ($reserva->observaciones ?? 'Sin observaciones'))
            ->line('')
            ->action('Ver Reserva en el Sistema', url('/admin/reservas/' . $reserva->id))
            ->line('Por favor, asegúrese de que el recinto esté preparado para la fecha y hora indicadas.')
            ->salutation('Atentamente, Municipalidad de Arica - Sistema de Reservas de Recintos Deportivos');
    }

    /**
     * array solamente
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