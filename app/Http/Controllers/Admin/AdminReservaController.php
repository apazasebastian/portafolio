<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reserva;
use App\Models\Recinto;
use App\Models\User;
use App\Models\AuditLog;
use App\Mail\ReservaAprobada;
use App\Mail\ReservaRechazada;
use App\Notifications\ReservaAprobadaEncargado;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

/**
 * Controlador de Administracion de Reservas
 * 
 * Este controlador es usado por el personal administrativo (Jefe de Recintos)
 * para revisar, aprobar o rechazar las solicitudes de reserva enviadas
 * por los ciudadanos.
 */
class AdminReservaController extends Controller
{
    /**
     * Muestra el listado de todas las reservas con opciones de filtro
     * 
     * El Jefe de Recintos usa esta pagina para ver todas las solicitudes
     * de reserva y puede filtrar por estado (pendiente, aprobada, etc.),
     * por recinto o por fecha especifica.
     */
    public function index(Request $request)
    {
        // Inicia la consulta incluyendo los datos del recinto asociado
        $query = Reserva::with(['recinto']);

        // Filtro por estado: pendiente, aprobada, rechazada o cancelada
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filtro por recinto: muestra solo las reservas de un recinto especifico
        if ($request->filled('recinto_id')) {
            $query->where('recinto_id', $request->recinto_id);
        }

        // Filtro por fecha: muestra solo las reservas de un dia especifico
        if ($request->filled('fecha')) {
            $query->whereDate('fecha_reserva', $request->fecha);
        }

        // Ordena las reservas de la mas reciente a la mas antigua
        $reservas = $query->orderBy('fecha_reserva', 'desc')
            ->paginate(15)
            ->withQueryString();
        
        // Carga la lista de recintos para el menu de filtro
        $recintos = Recinto::all();
        
        return view('admin.reservas.index', compact('reservas', 'recintos'));
    }

    /**
     * Muestra los detalles completos de una reserva
     * 
     * Esta pagina muestra toda la informacion de la reserva incluyendo
     * los datos del solicitante, y desde aqui el administrador puede
     * aprobar o rechazar la solicitud.
     */
    public function show(Reserva $reserva)
    {
        // Carga la informacion del recinto y quien aprobo la reserva
        $reserva->load(['recinto', 'aprobadaPor']);
        
        return view('admin.reservas.show', compact('reserva'));
    }

    /**
     * Aprueba una solicitud de reserva
     * 
     * Cuando el Jefe de Recintos aprueba una reserva:
     * 1. Cambia el estado de la reserva a "aprobada"
     * 2. Genera un codigo unico que el ciudadano puede usar para cancelar
     * 3. Envia un correo de confirmacion al solicitante
     * 4. Notifica al encargado del recinto para que este preparado
     * 5. Registra la accion en el historial de auditoria
     */
    public function aprobar(Reserva $reserva)
    {
        // Solo el Jefe de Recintos puede aprobar reservas
        if (auth()->user()->role !== 'jefe_recintos') {
            return redirect()->back()->with('error', 'No tienes permisos para aprobar reservas.');
        }

        // Solo se pueden aprobar reservas que estan pendientes de revision
        if ($reserva->estado !== 'pendiente') {
            return redirect()->back()->with('error', 'Solo se pueden aprobar reservas pendientes.');
        }

        // Guarda el estado anterior para el registro de auditoria
        $oldValues = [
            'estado' => $reserva->estado,
            'codigo_cancelacion' => $reserva->codigo_cancelacion,
        ];

        // Genera un codigo unico para que el ciudadano pueda cancelar si lo necesita
        if (!$reserva->codigo_cancelacion) {
            $reserva->codigo_cancelacion = $this->generarCodigoCancelacion();
        }
        
        // Actualiza la reserva con el nuevo estado
        $reserva->estado = 'aprobada';
        $reserva->fecha_respuesta = now();
        $reserva->aprobada_por = auth()->id();
        $reserva->save();

        // Envia correo de confirmacion al ciudadano que hizo la reserva
        try {
            Mail::to($reserva->email)->send(new ReservaAprobada($reserva));
            \Log::info("Correo de aprobación enviado al solicitante: {$reserva->email}");
        } catch (\Exception $e) {
            \Log::error('Error enviando correo de aprobación al solicitante: ' . $e->getMessage());
        }

        // Notifica al encargado del recinto para que sepa de la reserva
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

        // Registra esta accion en el historial de auditoria
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
     * Rechaza una solicitud de reserva
     * 
     * Cuando el Jefe de Recintos rechaza una reserva:
     * 1. Requiere que indique el motivo del rechazo
     * 2. Cambia el estado de la reserva a "rechazada"
     * 3. Envia un correo al solicitante explicando el motivo
     * 4. Registra la accion en el historial de auditoria
     */
    public function rechazar(Request $request, Reserva $reserva)
    {
        // Solo el Jefe de Recintos puede rechazar reservas
        if (auth()->user()->role !== 'jefe_recintos') {
            return redirect()->back()->with('error', 'No tienes permisos para rechazar reservas.');
        }

        // Solo se pueden rechazar reservas que estan pendientes
        if ($reserva->estado !== 'pendiente') {
            return redirect()->back()->with('error', 'Solo se pueden rechazar reservas pendientes.');
        }

        // El motivo de rechazo es obligatorio para informar al solicitante
        $request->validate([
            'motivo_rechazo' => 'required|string|max:500'
        ], [
            'motivo_rechazo.required' => 'Debes proporcionar un motivo de rechazo'
        ]);

        // Guarda el estado anterior para el registro de auditoria
        $oldValues = [
            'estado' => $reserva->estado,
            'motivo_rechazo' => $reserva->motivo_rechazo,
        ];
        
        // Actualiza la reserva con el nuevo estado y motivo
        $reserva->estado = 'rechazada';
        $reserva->fecha_respuesta = now();
        $reserva->aprobada_por = auth()->id();
        $reserva->motivo_rechazo = $request->motivo_rechazo;
        $reserva->save();

        // Envia correo al solicitante informando del rechazo y el motivo
        try {
            Mail::to($reserva->email)->send(new ReservaRechazada($reserva));
        } catch (\Exception $e) {
            \Log::error('Error enviando correo de rechazo: ' . $e->getMessage());
        }

        // Registra esta accion en el historial de auditoria
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
     * Genera un codigo unico para cancelacion de reservas
     * 
     * Este codigo es como una "clave secreta" que se entrega al ciudadano
     * cuando su reserva es aprobada. La puede usar para cancelar la reserva
     * si ya no puede asistir.
     */
    private function generarCodigoCancelacion()
    {
        // Genera codigos hasta encontrar uno que no exista en la base de datos
        do {
            $codigo = strtoupper(Str::random(8) . '-' . Str::random(8));
        } while (Reserva::where('codigo_cancelacion', $codigo)->exists());
        
        return $codigo;
    }
}