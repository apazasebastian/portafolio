<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactoNotificacion extends Mailable
{
    use Queueable, SerializesModels;

    public $datos;

    /**
     * Crea un nuevo mensaje en la instancia.
     */
    public function __construct($datos)
    {
        $this->datos = $datos;
    }

    /**
     * Construye el mensaje.
     */
    public function build()
    {
        return $this->subject('Nueva Consulta de Contacto - ' . $this->datos['nombreRecinto'])
                    ->view('emails.contacto-notificacion')
                    ->with([
                        'datos' => $this->datos
                    ]);
    }
}
