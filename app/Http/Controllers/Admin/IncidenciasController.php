<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Incidencia;
use App\Models\Recinto;
use App\Models\Reserva;
use App\Models\AuditLog;
use Illuminate\Http\Request;

/**
 * Controlador de Incidencias
 * 
 * Permite a los encargados de recinto reportar incidencias despues de que
 * se use un recinto. Por ejemplo: si el grupo no asistio, si dejaron el
 * recinto en mal estado, o si hubo algun dano en las instalaciones.
 */
class IncidenciasController extends Controller
{
    /**
     * Página de Reporte Diario
     * 
     * Muestra tarjetas de instalaciones con opción de reportar incidencia
     * y un historial de reportes con filtros y paginación.
     */
    public function index(Request $request)
    {
        // Obtener todos los recintos activos
        $recintos = Recinto::where('activo', true)->get();

        // Para cada recinto, encontrar la última reserva que permite reportar incidencia
        $recintos->each(function ($recinto) {
            $recinto->ultimaReservaReportable = Reserva::where('recinto_id', $recinto->id)
                ->where('estado', 'aprobada')
                ->where('fecha_reserva', '<', now()->toDateString())
                ->orderBy('fecha_reserva', 'desc')
                ->first();
        });

        // Consulta de incidencias con filtros
        $query = Incidencia::with(['reserva.recinto', 'reserva.aprobadaPor', 'recinto'])
            ->orderBy('created_at', 'desc');

        // Filtro por fecha
        if ($request->filled('fecha')) {
            $query->whereDate('created_at', $request->fecha);
        }

        // Filtro por recinto
        if ($request->filled('recinto_id')) {
            $query->whereHas('reserva', function ($q) use ($request) {
                $q->where('recinto_id', $request->recinto_id);
            });
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $incidencias = $query->paginate(10)->appends($request->query());

        return view('admin.incidencias.index', compact('recintos', 'incidencias'));
    }

    /**
     * Muestra el formulario para reportar una nueva incidencia
     * 
     * Carga todas las reservas reportables (aprobadas y finalizadas).
     * Si se pasa recinto_id como query param, filtra por ese recinto.
     * Si se pasa reservaId en la URL, lo pre-selecciona.
     */
    public function crear(Request $request, $reservaId = null)
    {
        // Cargar reservas reportables SIN incidencia reportada, del más reciente al más antiguo
        $query = Reserva::with('recinto')
            ->where('estado', 'aprobada')
            ->where('fecha_reserva', '<=', now()->toDateString())
            ->whereNull('fecha_cancelacion')
            ->doesntHave('incidencias')
            ->orderBy('fecha_reserva', 'desc')
            ->orderBy('hora_inicio', 'desc');

        // Filtrar por recinto si viene de la página de reporte
        if ($request->filled('recinto_id')) {
            $query->where('recinto_id', $request->recinto_id);
        }

        $reservas = $query->limit(50)->get();

        // Pre-seleccionar reserva si viene por URL
        $selectedReservaId = $reservaId ?? old('reserva_id');

        return view('admin.incidencias.crear', compact('reservas', 'selectedReservaId'));
    }

    /**
     * Registra un reporte sin incidencia para un recinto.
     * 
     * Crea un registro tipo 'informe' con estado 'conforme'
     * sin referencia a ninguna reserva específica.
     */
    public function reporteSinIncidencia(Request $request)
    {
        $request->validate([
            'recinto_id' => 'required|exists:recintos,id',
        ]);

        $recinto = \App\Models\Recinto::findOrFail($request->recinto_id);
        $usuario = auth()->user();

        // Solo puede haber un informe de conformidad por recinto por día.
        // Si ya existe uno del mismo día, se elimina antes de crear el nuevo.
        Incidencia::where('recinto_id', $recinto->id)
            ->where('tipo', 'informe')
            ->whereDate('created_at', now()->toDateString())
            ->delete();

        $fechaHoy = now()->locale('es')->isoFormat('D [de] MMMM [de] YYYY');
        $descripcion = "INFORME DE CONFORMIDAD\n";
        $descripcion .= "======================\n\n";
        $descripcion .= "Recinto: {$recinto->nombre}\n";
        $descripcion .= "Fecha del reporte: {$fechaHoy}\n";
        $descripcion .= "Responsable: {$usuario->email}\n\n";
        $descripcion .= "---\n\n";
        $descripcion .= "Se certifica que el recinto deportivo \"{$recinto->nombre}\" se encuentra en condiciones normales de operación. ";
        $descripcion .= "No se registraron incidencias, daños ni novedades durante el período evaluado. ";
        $descripcion .= "Las instalaciones se encuentran en buen estado y aptas para su uso habitual.";

        $incidencia = Incidencia::create([
            'reserva_id' => null,
            'recinto_id' => $recinto->id,
            'tipo' => 'informe',
            'descripcion' => $descripcion,
            'estado' => 'conforme',
            'reportado_por' => $usuario->email,
            'imagenes' => null,
        ]);

        AuditLog::log(
            action: 'crear_informe_conformidad',
            description: "Registró informe de conformidad para {$recinto->nombre}",
            auditable: $incidencia,
            newValues: [
                'tipo' => 'informe',
                'estado' => 'conforme',
                'recinto' => $recinto->nombre,
                'reportado_por' => $usuario->email,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => "Informe de conformidad registrado para {$recinto->nombre}.",
        ]);
    }

    /**
     * Guarda una nueva incidencia en el sistema
     * 
     * Valida los datos del formulario y crea el registro de incidencia.
     * Ya no usa reservaId de la URL, sino del campo reserva_id del formulario.
     */
    public function store(Request $request)
    {
        // Valida todos los campos del formulario
        $validated = $request->validate([
            'reserva_id' => 'required|exists:reservas,id',
            'tipo' => 'required|in:problema_posuso,dano,otro',
            'descripcion' => 'required|string|min:10|max:1000',
            'estado_recinto' => 'nullable|in:buen_estado,mal_estado',
            'cantidad_personas' => 'nullable|integer|min:1|max:500',
            'hora_inicio_real' => 'nullable|date_format:H:i',
            'hora_fin_real' => 'nullable|date_format:H:i|after:hora_inicio_real',
            'imagenes' => 'nullable|array|max:5',
            'imagenes.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'reserva_id.required' => 'Debe seleccionar un evento',
            'reserva_id.exists' => 'El evento seleccionado no es válido',
            'tipo.required' => 'Debe seleccionar un tipo de incidencia',
            'tipo.in' => 'El tipo de incidencia seleccionado no es válido',
            'descripcion.required' => 'La descripción de la incidencia es obligatoria',
            'descripcion.min' => 'La descripción debe tener al menos 10 caracteres',
            'descripcion.max' => 'La descripción no puede exceder 1000 caracteres',
            'estado_recinto.in' => 'El estado del recinto no es válido',
            'cantidad_personas.integer' => 'La cantidad de personas debe ser un número',
            'cantidad_personas.min' => 'Debe haber al menos 1 persona',
            'cantidad_personas.max' => 'No puede haber más de 500 personas',
            'hora_inicio_real.date_format' => 'La hora de inicio debe tener el formato HH:MM',
            'hora_fin_real.date_format' => 'La hora de finalización debe tener el formato HH:MM',
            'hora_fin_real.after' => 'La hora de finalización debe ser posterior a la hora de inicio',
            'imagenes.max' => 'Solo puede subir un máximo de 5 imágenes',
            'imagenes.*.image' => 'El archivo debe ser una imagen',
            'imagenes.*.mimes' => 'Las imágenes deben ser JPG, PNG o WebP',
            'imagenes.*.max' => 'Cada imagen no puede exceder 2MB',
        ]);

        $reserva = Reserva::with('recinto')->findOrFail($validated['reserva_id']);
        
        // Verificacion de seguridad: la reserva debe poder tener incidencia
        if (!$reserva->puedeReportarIncidencia()) {
            return redirect()->route('admin.incidencias.index')
                ->with('error', 'Esta reserva no puede tener incidencias reportadas.');
        }

        // Guardar imágenes
        $imagenesGuardadas = [];
        if ($request->hasFile('imagenes')) {
            foreach ($request->file('imagenes') as $imagen) {
                $nombreArchivo = 'incidencia_' . $reserva->id . '_' . time() . '_' . uniqid() . '.' . $imagen->getClientOriginalExtension();
                $ruta = $imagen->storeAs('incidencias', $nombreArchivo, 'public');
                $imagenesGuardadas[] = $ruta;
            }
        }
        
        // Construye la descripcion completa que se guardara
        $descripcionCompleta = "REPORTE DE INCIDENCIA\n";
        $descripcionCompleta .= "======================\n\n";
        
        if (!empty($validated['estado_recinto'])) {
            $descripcionCompleta .= "Estado del Recinto: " . ($validated['estado_recinto'] === 'buen_estado' ? 'Buen Estado' : 'Mal Estado') . "\n";
        }
        if (!empty($validated['cantidad_personas'])) {
            $descripcionCompleta .= "Cantidad de Personas: " . $validated['cantidad_personas'] . "\n";
        }
        if (!empty($validated['hora_inicio_real'])) {
            $descripcionCompleta .= "Hora Real de Inicio: " . $validated['hora_inicio_real'] . "\n";
        }
        if (!empty($validated['hora_fin_real'])) {
            $descripcionCompleta .= "Hora Real de Finalización: " . $validated['hora_fin_real'] . "\n";
        }
        if (count($imagenesGuardadas) > 0) {
            $descripcionCompleta .= "Imágenes adjuntas: " . count($imagenesGuardadas) . "\n";
        }
        
        $descripcionCompleta .= "\n---\n\n";
        $descripcionCompleta .= "Descripción de la Incidencia:\n";
        $descripcionCompleta .= $validated['descripcion'];
        
        // Crea el registro de incidencia en la base de datos
        $incidencia = Incidencia::create([
            'reserva_id' => $reserva->id,
            'tipo' => $validated['tipo'],
            'descripcion' => $descripcionCompleta,
            'estado' => 'reportada',
            'imagenes' => count($imagenesGuardadas) > 0 ? $imagenesGuardadas : null,
        ]);

        // Registra esta accion en el historial de auditoria
        AuditLog::log(
            action: 'crear_incidencia',
            description: "Creó incidencia de tipo '{$this->getNombreTipo($validated['tipo'])}' para la reserva #{$reserva->id} en {$reserva->recinto->nombre}",
            auditable: $incidencia,
            newValues: [
                'tipo' => $validated['tipo'],
                'estado_recinto' => $validated['estado_recinto'] ?? null,
                'cantidad_personas' => $validated['cantidad_personas'] ?? null,
                'hora_inicio_real' => $validated['hora_inicio_real'] ?? null,
                'hora_fin_real' => $validated['hora_fin_real'] ?? null,
                'descripcion' => $validated['descripcion'],
                'imagenes' => count($imagenesGuardadas) > 0 ? count($imagenesGuardadas) . ' imágenes' : null,
                'estado' => 'reportada',
                'reserva_id' => $reserva->id,
            ]
        );
        
        return redirect()->route('admin.incidencias.show', $incidencia)
            ->with('success', 'Incidencia reportada correctamente.');
    }

    /**
     * Muestra los detalles completos de una incidencia
     */
    public function show(Incidencia $incidencia)
    {
        // Carga los datos relacionados: reserva, recinto, quien aprobo
        $incidencia->load(['reserva.recinto', 'reserva.aprobadaPor', 'recinto']);
        
        return view('admin.incidencias.show', compact('incidencia'));
    }

    /**
     * Cambia el estado de una incidencia
     * 
     * Los estados posibles son:
     * - Reportada: recien creada, pendiente de revision
     * - En Revision: alguien ya la esta revisando
     * - Resuelta: el problema fue solucionado
     */
    public function cambiarEstado(Request $request, Incidencia $incidencia)
    {
        $validated = $request->validate([
            'estado' => 'required|in:reportada,en_revision,resuelta',
        ]);

        // Guarda el estado anterior para el registro de auditoria
        $oldEstado = $incidencia->estado;
        
        $incidencia->update([
            'estado' => $validated['estado'],
        ]);

        // Registra el cambio en el historial
        AuditLog::log(
            action: 'cambiar_estado_incidencia',
            description: "Cambió el estado de la incidencia #{$incidencia->id} de '{$this->getNombreEstado($oldEstado)}' a '{$this->getNombreEstado($validated['estado'])}'",
            auditable: $incidencia,
            oldValues: ['estado' => $oldEstado],
            newValues: ['estado' => $validated['estado']]
        );
        
        return redirect()->back()
            ->with('success', 'Estado de la incidencia actualizado correctamente.');
    }

    /**
     * Elimina una incidencia del sistema
     */
    public function destroy(Incidencia $incidencia)
    {
        // Guarda los datos antes de eliminar para el registro
        $reservaId = $incidencia->reserva_id;
        $incidenciaId = $incidencia->id;
        $tipo = $incidencia->tipo;
        $descripcion = $incidencia->descripcion;
        $estado = $incidencia->estado;

        // Registra la eliminacion en el historial
        AuditLog::log(
            action: 'eliminar_incidencia',
            description: "Eliminó la incidencia #{$incidenciaId} (Tipo: {$this->getNombreTipo($tipo)}, Estado: {$this->getNombreEstado($estado)})",
            auditable: null,
            oldValues: [
                'id' => $incidenciaId,
                'tipo' => $tipo,
                'descripcion' => $descripcion,
                'estado' => $estado,
                'reserva_id' => $reservaId,
            ]
        );

        $incidencia->delete();
        
        return redirect()->route('admin.incidencias.crear', $reservaId)
            ->with('success', 'Incidencia eliminada correctamente.');
    }

    // =========================================================================
    // FUNCIONES AUXILIARES - Convierten codigos a nombres legibles
    // =========================================================================

    /**
     * Convierte el codigo del tipo de incidencia a un nombre legible
     */
    private function getNombreTipo($tipo)
    {
        return match($tipo) {
            'problema_posuso' => 'Problema Post-Uso',
            'dano' => 'Daño en Instalaciones',
            'otro' => 'Otro',
            default => $tipo,
        };
    }

    /**
     * Convierte el codigo de estado a un nombre legible
     */
    private function getNombreEstado($estado)
    {
        return match($estado) {
            'reportada' => 'Reportada',
            'en_revision' => 'En Revisión',
            'resuelta' => 'Resuelta',
            default => $estado,
        };
    }
}