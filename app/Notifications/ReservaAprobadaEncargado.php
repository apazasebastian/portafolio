<?php

namespace App\Notifications;

use App\Models\Reserva;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 *  Notificación que se envía al encargado del recinto cuando se APRUEBA una reserva
 * Esta clase define el formato y contenido del email que recibe el encargado
 */
class ReservaAprobadaEncargado extends Notification
{
    // Permite encolar esta notificación para enviarla de manera asincrónica
    use Queueable;

    // Variable privada que almacena la reserva aprobada
    protected $reserva;

    /**
     *  Constructor de la clase
     * Recibe la reserva aprobada y la almacena para usarla en los métodos de notificación
     * 
     * @param Reserva $reserva - La reserva que fue aprobada
     */
    public function __construct(Reserva $reserva)
    {
        $this->reserva = $reserva;
    }

    /**
     * Define a través de qué canales se envía la notificación
     * En este caso, solo por EMAIL ['mail']
     * Podría ser también SMS, Slack, etc.
     * 
     * @param object $notifiable - El usuario que recibe la notificación (encargado del recinto)
     * @return array - Array con los canales de envío
     */
    public function via(object $notifiable): array
    {
        return ['mail'];  // Enviar solo por correo electrónico
    }

    /**
     *  Construye el contenido del EMAIL que se envía
     * Define el asunto, cuerpo, botones y toda la estructura del correo
     * 
     * @param object $notifiable - El usuario (encargado) que recibe el email
     * @return MailMessage - El objeto que contiene la estructura del email
     */
    public function toMail(object $notifiable): MailMessage
    {
        $reserva = $this->reserva;
        $recinto = $reserva->recinto;
        
        return (new MailMessage)
            // Asunto del email
            ->subject('Nueva Reserva Aprobada - ' . $recinto->nombre)
            
            // Saludo personalizado con el nombre del encargado
            ->greeting('Estimado/a ' . $notifiable->name . ',')
            
            // Cuerpo principal del email
            ->line('Se ha aprobado una nueva reserva para el recinto ' . $recinto->nombre . ' del cual usted es encargado.')
            ->line('')
            
            // Sección de detalles
            ->line('DETALLES DE LA RESERVA:')
            ->line('')
            
            // Información de la organización/solicitante
            ->line('Organización: ' . $reserva->nombre_organizacion)
            ->line('Solicitante: ' . $reserva->representante_nombre)
            ->line('Correo electrónico: ' . $reserva->email)
            ->line('Teléfono: ' . $reserva->telefono)
            ->line('')
            
            // Información de la reserva (fecha, hora, deporte, personas)
            ->line('Fecha: ' . \Carbon\Carbon::parse($reserva->fecha_reserva)->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY'))
            ->line('Horario: ' . substr($reserva->hora_inicio, 0, 5) . ' - ' . substr($reserva->hora_fin, 0, 5) . ' hrs.')
            ->line('Deporte/Actividad: ' . ($reserva->deporte ?? 'No especificado'))
            ->line('Número de participantes: ' . $reserva->cantidad_personas . ' personas')
            ->line('')
            
            // Observaciones adicionales (si las hay)
            ->line('Observaciones: ' . ($reserva->observaciones ?? 'Sin observaciones'))
            ->line('')
            
            // Botón de acción que redirige al sistema para ver detalles
            ->action('Ver Reserva en el Sistema', url('/admin/reservas/' . $reserva->id))
            
            // Nota importante para el encargado
            ->line('Por favor, asegúrese de que el recinto esté preparado para la fecha y hora indicadas.')
            
            // Despedida profesional del email
            ->salutation('Atentamente, Municipalidad de Arica - Sistema de Reservas de Recintos Deportivos');
    }

    /**
     *  Convierte la notificación a un array para almacenamiento en base de datos
     * Se usa si guardas las notificaciones en la BD (tabla notifications)
     * 
     * @param object $notifiable - El usuario que recibe la notificación
     * @return array - Array con los datos a guardar
     */
    public function toArray(object $notifiable): array
    {
        return [
            'reserva_id' => $this->reserva->id,           // ID de la reserva aprobada
            'recinto_id' => $this->reserva->recinto_id,   // ID del recinto
            'fecha_reserva' => $this->reserva->fecha_reserva,  // Fecha de la reserva
        ];
    }
}