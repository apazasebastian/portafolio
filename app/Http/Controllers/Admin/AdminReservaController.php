<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reserva;
use App\Models\Recinto;
use App\Models\AuditLog; // ← NUEVO
use App\Mail\ReservaAprobada;
use App\Mail\ReservaRechazada;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class AdminReservaController extends Controller
{
    /**
     * Mostrar listado de reservas con filtros activos
     */
    public function index(Request $request)
    {
        // Iniciamos la consulta cargando la relación con el recinto
        $query = Reserva::with(['recinto']);

        // 1. Filtro por Estado (Pendiente, Aprobada, Rechazada, Cancelada)
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // 2. Filtro por Recinto
        if ($request->filled('recinto_id')) {
            $query->where('recinto_id', $request->recinto_id);
        }

        // 3. Filtro por Fecha específica
        if ($request->filled('fecha')) {
            $query->whereDate('fecha_reserva', $request->fecha);
        }

        // Ordenamos por fecha descendente y paginamos
        $reservas = $query->orderBy('fecha_reserva', 'desc')
            ->paginate(15)
            ->withQueryString();
        
        // Cargamos los recintos para el menú desplegable del filtro
        $recintos = Recinto::all();
        
        return view('admin.reservas.index', compact('reservas', 'recintos'));
    }

    /**
     * Mostrar detalles de una reserva específica.
     */
    public function show(Reserva $reserva)
    {
        // Cargar relaciones necesarias para la vista
        $reserva->load(['recinto', 'aprobadaPor']);
        
        return view('admin.reservas.show', compact('reserva'));
    }

    /**
     * Aprobar una reserva.
     */
    public function aprobar(Reserva $reserva)
    {
        // Guardar valores anteriores para auditoría
        $oldValues = [
            'estado' => $reserva->estado,
            'codigo_cancelacion' => $reserva->codigo_cancelacion,
        ];

        // Generar código de cancelación si no existe
        if (!$reserva->codigo_cancelacion) {
            $reserva->codigo_cancelacion = $this->generarCodigoCancelacion();
        }
        
        // Actualizar estado
        $reserva->estado = 'aprobada';
        $reserva->fecha_respuesta = now();
        $reserva->aprobada_por = auth()->id();
        $reserva->save();

        //  REGISTRAR EN AUDITORÍA 
        AuditLog::log(
            action: 'aprobar_reserva',
            description: "Aprobó la reserva #{$reserva->id} de {$reserva->nombre_organizacion} para {$reserva->recinto->nombre} el día {$reserva->fecha_reserva->format('d/m/Y')}",
            auditable: $reserva,
            oldValues: $oldValues,
            newValues: [
                'estado' => 'aprobada',
                'fecha_respuesta' => now()->toDateTimeString(),
                'aprobada_por' => auth()->id(),
                'codigo_cancelacion' => $reserva->codigo_cancelacion,
            ]
        );
        
        // Enviar correo de aprobación
        try {
            Mail::to($reserva->email)->send(new ReservaAprobada($reserva));
        } catch (\Exception $e) {
            \Log::error('Error enviando correo de aprobación: ' . $e->getMessage());
        }
        
        return redirect()->route('admin.reservas.show', $reserva)
            ->with('success', 'Reserva aprobada correctamente y correo enviado.');
    }

    /**
     * Rechazar una reserva.
     */
    public function rechazar(Request $request, Reserva $reserva)
    {
        $request->validate([
            'motivo_rechazo' => 'required|string|max:500'
        ]);

        // Guardar valores anteriores para auditoría
        $oldValues = [
            'estado' => $reserva->estado,
            'motivo_rechazo' => $reserva->motivo_rechazo,
        ];
        
        $reserva->estado = 'rechazada';
        $reserva->fecha_respuesta = now();
        $reserva->motivo_rechazo = $request->motivo_rechazo;
        $reserva->save();

        // REGISTRAR EN AUDITORÍA 
        AuditLog::log(
            action: 'rechazar_reserva',
            description: "Rechazó la reserva #{$reserva->id} de {$reserva->nombre_organizacion} para {$reserva->recinto->nombre}. Motivo: {$request->motivo_rechazo}",
            auditable: $reserva,
            oldValues: $oldValues,
            newValues: [
                'estado' => 'rechazada',
                'motivo_rechazo' => $request->motivo_rechazo,
                'fecha_respuesta' => now()->toDateTimeString(),
            ]
        );
        
        // Enviar correo de rechazo
        try {
            Mail::to($reserva->email)->send(new ReservaRechazada($reserva));
        } catch (\Exception $e) {
            \Log::error('Error enviando correo de rechazo: ' . $e->getMessage());
        }
        
        return redirect()->route('admin.reservas.show', $reserva)
            ->with('success', 'Reserva rechazada y notificación enviada.');
    }

    /**
     * Genera un código único de cancelación.
     */
    private function generarCodigoCancelacion()
    {
        do {
            $codigo = strtoupper(Str::random(8) . '-' . Str::random(8));
        } while (Reserva::where('codigo_cancelacion', $codigo)->exists());
        
        return $codigo;
    }
}