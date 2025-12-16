<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reserva;
use App\Models\Recinto;
use App\Models\User;
use App\Models\AuditLog;
use App\Mail\ReservaAprobada;
use App\Mail\ReservaRechazada;
use App\Notifications\ReservaAprobadaEncargado; //  NUEVA NOTIFICACIÓN 
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
     *  ACTUALIZADO: Aprobar reserva y notificar a solicitante + encargado 
     */
    public function aprobar(Reserva $reserva)
    {
        // Verificar que el usuario sea jefe de recintos
        if (auth()->user()->role !== 'jefe_recintos') {
            return redirect()->back()->with('error', 'No tienes permisos para aprobar reservas.');
        }

        // Verificar que la reserva esté pendiente
        if ($reserva->estado !== 'pendiente') {
            return redirect()->back()->with('error', 'Solo se pueden aprobar reservas pendientes.');
        }

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

        //  1. ENVIAR CORREO AL SOLICITANTE
        try {
            Mail::to($reserva->email)->send(new ReservaAprobada($reserva));
            \Log::info("Correo de aprobación enviado al solicitante: {$reserva->email}");
        } catch (\Exception $e) {
            \Log::error('Error enviando correo de aprobación al solicitante: ' . $e->getMessage());
        }

        //  2. ENVIAR NOTIFICACIÓN AL ENCARGADO DEL RECINTO (NUEVO)
        try {
            $encargado = User::where('role', 'encargado_recinto')
                ->where('recinto_asignado_id', $reserva->recinto_id)
                ->where('activo', true)
                ->first();

            if ($encargado) {
                $encargado->notify(new ReservaAprobadaEncargado($reserva));
                \Log::info("Notificación enviada al encargado {$encargado->email} para reserva #{$reserva->id}");
            } else {
                \Log::warning("No se encontró encargado activo para el recinto #{$reserva->recinto_id}");
            }
        } catch (\Exception $e) {
            \Log::error('Error al enviar notificación al encargado: ' . $e->getMessage());
        }

        //  3. REGISTRAR EN AUDITORÍA
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
        
        return redirect()->route('admin.reservas.show', $reserva)
            ->with('success', 'Reserva aprobada correctamente. Se ha enviado confirmación al solicitante y notificación al encargado del recinto.');
    }

    /**
     * Rechazar una reserva.
     */
    public function rechazar(Request $request, Reserva $reserva)
    {
        // Verificar que el usuario sea jefe de recintos
        if (auth()->user()->role !== 'jefe_recintos') {
            return redirect()->back()->with('error', 'No tienes permisos para rechazar reservas.');
        }

        // Verificar que la reserva esté pendiente
        if ($reserva->estado !== 'pendiente') {
            return redirect()->back()->with('error', 'Solo se pueden rechazar reservas pendientes.');
        }

        $request->validate([
            'motivo_rechazo' => 'required|string|max:500'
        ], [
            'motivo_rechazo.required' => 'Debes proporcionar un motivo de rechazo'
        ]);

        // Guardar valores anteriores para auditoría
        $oldValues = [
            'estado' => $reserva->estado,
            'motivo_rechazo' => $reserva->motivo_rechazo,
        ];
        
        $reserva->estado = 'rechazada';
        $reserva->fecha_respuesta = now();
        $reserva->aprobada_por = auth()->id();
        $reserva->motivo_rechazo = $request->motivo_rechazo;
        $reserva->save();

        // Enviar correo de rechazo
        try {
            Mail::to($reserva->email)->send(new ReservaRechazada($reserva));
        } catch (\Exception $e) {
            \Log::error('Error enviando correo de rechazo: ' . $e->getMessage());
        }

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
                'aprobada_por' => auth()->id(),
            ]
        );
        
        return redirect()->route('admin.reservas.show', $reserva)
            ->with('success', 'Reserva rechazada y notificación enviada al solicitante.');
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