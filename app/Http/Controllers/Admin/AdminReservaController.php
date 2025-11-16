<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reserva;
use App\Mail\ReservaAprobada;
use App\Mail\ReservaRechazada;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class AdminReservaController extends Controller
{
    // ... otros métodos ...

    /**
     * Aprobar una reserva
     */
    public function aprobar(Reserva $reserva)
    {
        // Generar código de cancelación si no existe
        if (!$reserva->codigo_cancelacion) {
            // CORRECCIÓN: Llamar al método del controlador, no del modelo
            $reserva->codigo_cancelacion = $this->generarCodigoCancelacion();
        }
        
        // Actualizar estado
        $reserva->estado = 'aprobada';
        $reserva->fecha_respuesta = now();
        $reserva->aprobada_por = auth()->id();
        $reserva->save();
        
        // Enviar correo de aprobación con el código
        try {
            Mail::to($reserva->email)->send(new ReservaAprobada($reserva));
        } catch (\Exception $e) {
            \Log::error('Error enviando correo de aprobación: ' . $e->getMessage());
        }
        
        return redirect()->route('admin.dashboard')
            ->with('success', 'Reserva aprobada correctamente y correo enviado.');
    }

    /**
     * Rechazar una reserva
     */
    public function rechazar(Request $request, Reserva $reserva)
    {
        $request->validate([
            'motivo_rechazo' => 'required|string|max:500'
        ]);
        
        $reserva->estado = 'rechazada';
        $reserva->fecha_respuesta = now();
        $reserva->motivo_rechazo = $request->motivo_rechazo;
        $reserva->save();
        
        // Enviar correo de rechazo
        try {
            Mail::to($reserva->email)->send(new ReservaRechazada($reserva));
        } catch (\Exception $e) {
            \Log::error('Error enviando correo de rechazo: ' . $e->getMessage());
        }
        
        return redirect()->route('admin.dashboard')
            ->with('success', 'Reserva rechazada y notificación enviada.');
    }

    /**
     * Genera un código único de cancelación
     * IMPORTANTE: Este método debe estar en el CONTROLADOR, no en el modelo
     */
    private function generarCodigoCancelacion()
    {
        do {
            $codigo = strtoupper(Str::random(8) . '-' . Str::random(8));
        } while (Reserva::where('codigo_cancelacion', $codigo)->exists());
        
        return $codigo;
    }
    
    // ... otros métodos ...
}