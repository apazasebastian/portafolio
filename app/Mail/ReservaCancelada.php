<?php

namespace App\Mail;

use App\Models\Reserva;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class ReservaCancelada extends Mailable
{
    use Queueable, SerializesModels;

    public $reserva;

    public function __construct(Reserva $reserva)
    {
        $this->reserva = $reserva;
    }

    public function build()
    {
        // Formatear la fecha para el subject
        $fecha = Carbon::parse($this->reserva->fecha_reserva)->format('d/m/Y');
        
        return $this->subject('Confirmación de Cancelación - ' . $this->reserva->recinto->nombre . ' (' . $fecha . ')')
                    ->view('emails.reserva-cancelada')
                    ->with([
                        'reserva' => $this->reserva,
                        'fecha_formateada' => $fecha,
                        'horario' => $this->reserva->hora_inicio . ' - ' . $this->reserva->hora_fin
                    ]);
    }
}