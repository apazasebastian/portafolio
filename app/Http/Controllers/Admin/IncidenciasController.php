<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Incidencia;
use App\Models\Reserva;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class IncidenciasController extends Controller
{
    /**
     * Redirigir al dashboard (no hay vista de listado)
     */
    public function index()
    {
        return redirect()->route('admin.dashboard');
    }

    /**
     * Mostrar formulario para crear incidencia
     */
    public function crear($reservaId)
    {
        $reserva = Reserva::with(['recinto', 'incidencias'])->findOrFail($reservaId);
        
        // Validar que la reserva pueda tener incidencia reportada
        if (!$reserva->puedeReportarIncidencia()) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Esta reserva no puede tener incidencias reportadas. Debe estar aprobada y finalizada.');
        }
        
        return view('admin.incidencias.crear', compact('reserva'));
    }

    /**
     * Guardar nueva incidencia
     */
    public function store(Request $request, $reservaId)
    {
        $reserva = Reserva::findOrFail($reservaId);
        
        // Validar que la reserva pueda tener incidencia
        if (!$reserva->puedeReportarIncidencia()) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Esta reserva no puede tener incidencias reportadas.');
        }
        
        // ✅ VALIDACIÓN EN DOS PASOS: Base + Condicional
        // PASO 1: Validar campos base
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

        // PASO 2: Validar campos condicionales solo si asistieron = SÍ
        $validated = $baseValidated;
        
        if ($baseValidated['asistieron'] === 'si') {
            $additionalValidated = $request->validate([
                'estado_recinto' => 'required|in:buen_estado,mal_estado',
                'cantidad_personas' => 'required|integer|min:1|max:500',
                'hora_inicio_real' => 'required|date_format:H:i',
                'hora_fin_real' => 'required|date_format:H:i|after:hora_inicio_real',
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
            ]);
            
            $validated = array_merge($baseValidated, $additionalValidated);
        }
        
        // ✅ CONSTRUIR DESCRIPCIÓN COMPLETA
        $descripcionCompleta = "REPORTE DE INCIDENCIA\n";
        $descripcionCompleta .= "======================\n\n";
        
        // Agregar información de asistencia
        $descripcionCompleta .= "¿Asistieron?: " . ($validated['asistieron'] === 'si' ? 'SÍ' : 'NO') . "\n";
        
        // Si asistieron, agregar campos adicionales
        if ($validated['asistieron'] === 'si') {
            $descripcionCompleta .= "Estado del Recinto: " . ($validated['estado_recinto'] === 'buen_estado' ? 'Buen Estado' : 'Mal Estado') . "\n";
            $descripcionCompleta .= "Cantidad de Personas: " . $validated['cantidad_personas'] . "\n";
            $descripcionCompleta .= "Hora Real de Inicio: " . $validated['hora_inicio_real'] . "\n";
            $descripcionCompleta .= "Hora Real de Finalización: " . $validated['hora_fin_real'] . "\n";
        }
        
        $descripcionCompleta .= "\n---\n\n";
        $descripcionCompleta .= "Descripción de la Incidencia:\n";
        $descripcionCompleta .= $validated['descripcion'];
        
        // Crear la incidencia
        $incidencia = Incidencia::create([
            'reserva_id' => $reserva->id,
            'tipo' => $validated['tipo'],
            'descripcion' => $descripcionCompleta,
            'estado' => 'reportada',
        ]);

        // REGISTRAR EN AUDITORÍA
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
                'estado' => 'reportada',
                'reserva_id' => $reserva->id,
            ]
        );
        
        return redirect()->route('admin.incidencias.show', $incidencia)
            ->with('success', 'Incidencia reportada correctamente.');
    }

    /**
     * Mostrar detalles de una incidencia
     */
    public function show(Incidencia $incidencia)
    {
        $incidencia->load(['reserva.recinto', 'reserva.aprobadaPor']);
        
        return view('admin.incidencias.show', compact('incidencia'));
    }

    /**
     * Cambiar estado de una incidencia
     */
    public function cambiarEstado(Request $request, Incidencia $incidencia)
    {
        $validated = $request->validate([
            'estado' => 'required|in:reportada,en_revision,resuelta',
        ]);

        // Guardar estado anterior
        $oldEstado = $incidencia->estado;
        
        $incidencia->update([
            'estado' => $validated['estado'],
        ]);

        // REGISTRAR EN AUDITORÍA
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
     * Eliminar incidencia
     */
    public function destroy(Incidencia $incidencia)
    {
        $reservaId = $incidencia->reserva_id;
        $incidenciaId = $incidencia->id;
        $tipo = $incidencia->tipo;
        $descripcion = $incidencia->descripcion;
        $estado = $incidencia->estado;

        // REGISTRAR EN AUDITORÍA ANTES DE ELIMINAR
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

    /**
     * Obtener nombre legible del tipo de incidencia
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
     * Obtener nombre legible del estado
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