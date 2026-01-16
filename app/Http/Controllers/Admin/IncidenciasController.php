<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Incidencia;
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
     * Redirige al panel principal (no hay listado de incidencias separado)
     */
    public function index()
    {
        return redirect()->route('admin.dashboard');
    }

    /**
     * Muestra el formulario para reportar una nueva incidencia
     * 
     * Solo se pueden reportar incidencias de reservas que ya terminaron
     * y que fueron aprobadas. Esto asegura que el reporte sea sobre
     * un uso real del recinto.
     */
    public function crear($reservaId)
    {
        // Busca la reserva con sus datos del recinto e incidencias previas
        $reserva = Reserva::with(['recinto', 'incidencias'])->findOrFail($reservaId);
        
        // Verifica que sea una reserva finalizada y aprobada
        if (!$reserva->puedeReportarIncidencia()) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Esta reserva no puede tener incidencias reportadas. Debe estar aprobada y finalizada.');
        }
        
        return view('admin.incidencias.crear', compact('reserva'));
    }

    /**
     * Guarda una nueva incidencia en el sistema
     * 
     * Valida los datos del formulario y crea el registro de incidencia.
     * Si el grupo si asistio, se piden datos adicionales como cuantas
     * personas habia y en que estado quedo el recinto.
     */
    public function store(Request $request, $reservaId)
    {
        $reserva = Reserva::findOrFail($reservaId);
        
        // Verificacion de seguridad: la reserva debe poder tener incidencia
        if (!$reserva->puedeReportarIncidencia()) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Esta reserva no puede tener incidencias reportadas.');
        }
        
        // Primero valida los campos basicos que siempre se requieren
        $baseValidated = $request->validate([
            'tipo' => 'required|in:problema_posuso,dano,otro',
            'asistieron' => 'required|in:si,no',
            'descripcion' => 'required|string|min:10|max:1000',
        ], [
            'tipo.required' => 'Debe seleccionar un tipo de incidencia',
            'tipo.in' => 'El tipo de incidencia seleccionado no es válido',
            'asistieron.required' => 'Debe indicar si asistieron o no',
            'asistieron.in' => 'La opción seleccionada no es válida',
            'descripcion.required' => 'La descripción de la incidencia es obligatoria',
            'descripcion.min' => 'La descripción debe tener al menos 10 caracteres',
            'descripcion.max' => 'La descripción no puede exceder 1000 caracteres',
        ]);

        // Si indicaron que si asistieron, valida campos adicionales
        $validated = $baseValidated;
        $imagenesGuardadas = [];
        
        if ($baseValidated['asistieron'] === 'si') {
            $additionalValidated = $request->validate([
                'estado_recinto' => 'required|in:buen_estado,mal_estado',
                'cantidad_personas' => 'required|integer|min:1|max:500',
                'hora_inicio_real' => 'required|date_format:H:i',
                'hora_fin_real' => 'required|date_format:H:i|after:hora_inicio_real',
                'imagenes' => 'nullable|array|max:5',
                'imagenes.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            ], [
                'estado_recinto.required' => 'Debe seleccionar el estado del recinto',
                'estado_recinto.in' => 'El estado del recinto no es válido',
                'cantidad_personas.required' => 'Debe ingresar la cantidad de personas',
                'cantidad_personas.integer' => 'La cantidad de personas debe ser un número',
                'cantidad_personas.min' => 'Debe haber al menos 1 persona',
                'cantidad_personas.max' => 'No puede haber más de 500 personas',
                'hora_inicio_real.required' => 'Debe ingresar la hora de inicio',
                'hora_inicio_real.date_format' => 'La hora de inicio debe tener el formato HH:MM',
                'hora_fin_real.required' => 'Debe ingresar la hora de finalización',
                'hora_fin_real.date_format' => 'La hora de finalización debe tener el formato HH:MM',
                'hora_fin_real.after' => 'La hora de finalización debe ser posterior a la hora de inicio',
                'imagenes.max' => 'Solo puede subir un máximo de 5 imágenes',
                'imagenes.*.image' => 'El archivo debe ser una imagen',
                'imagenes.*.mimes' => 'Las imágenes deben ser JPG, PNG o WebP',
                'imagenes.*.max' => 'Cada imagen no puede exceder 2MB',
            ]);
            
            $validated = array_merge($baseValidated, $additionalValidated);
            
            // Guarda las imagenes subidas
            if ($request->hasFile('imagenes')) {
                foreach ($request->file('imagenes') as $imagen) {
                    $nombreArchivo = 'incidencia_' . $reservaId . '_' . time() . '_' . uniqid() . '.' . $imagen->getClientOriginalExtension();
                    $ruta = $imagen->storeAs('incidencias', $nombreArchivo, 'public');
                    $imagenesGuardadas[] = $ruta;
                }
            }
        }
        
        // Construye la descripcion completa que se guardara
        $descripcionCompleta = "REPORTE DE INCIDENCIA\n";
        $descripcionCompleta .= "======================\n\n";
        $descripcionCompleta .= "¿Asistieron?: " . ($validated['asistieron'] === 'si' ? 'SÍ' : 'NO') . "\n";
        
        // Si asistieron, agrega los datos adicionales
        if ($validated['asistieron'] === 'si') {
            $descripcionCompleta .= "Estado del Recinto: " . ($validated['estado_recinto'] === 'buen_estado' ? 'Buen Estado' : 'Mal Estado') . "\n";
            $descripcionCompleta .= "Cantidad de Personas: " . $validated['cantidad_personas'] . "\n";
            $descripcionCompleta .= "Hora Real de Inicio: " . $validated['hora_inicio_real'] . "\n";
            $descripcionCompleta .= "Hora Real de Finalización: " . $validated['hora_fin_real'] . "\n";
            if (count($imagenesGuardadas) > 0) {
                $descripcionCompleta .= "Imágenes adjuntas: " . count($imagenesGuardadas) . "\n";
            }
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
                'asistieron' => $validated['asistieron'],
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
        $incidencia->load(['reserva.recinto', 'reserva.aprobadaPor']);
        
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