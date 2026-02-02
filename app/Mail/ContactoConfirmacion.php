<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactoConfirmacion extends Mailable
{
    use Queueable, SerializesModels;

    public $nombreUsuario;
    public $nombreRecinto;

    /**
     * Crea un nuevo mensaje en la instancia.
     */
    public function __construct($nombreUsuario, $nombreRecinto)
    {
        $this->nombreUsuario = $nombreUsuario;
        $this->nombreRecinto = $nombreRecinto;
    }

    /**
     * Construye el mensaje.
     */
    public function build()
    {
        return $this->subject('Confirmación de Contacto - Sistema de Reservas Deportivas')
                    ->view('emails.contacto-confirmacion')
                    ->with([
                        'nombreUsuario' => $this->nombreUsuario,
                        'nombreRecinto' => $this->nombreRecinto
                    ]);
    }
}
