<?php

namespace App\Mail;

use App\Models\Reserva;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReservaAprobada extends Mailable
{
    use Queueable, SerializesModels;

    public $reserva;

    /**
     * Crea un nuevo mensaje en la instancia.
     */
    public function __construct(Reserva $reserva)
    {
        $this->reserva = $reserva;
    }

    /**
     * Contruye.
     */
    public function build()
    {
        return $this->subject('Tu reserva ha sido aprobada - ' . $this->reserva->recinto->nombre)
                    ->view('emails.reserva-aprobada')
                    ->with([
                        'reserva' => $this->reserva
                    ]);
    }
}