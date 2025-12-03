<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Incidencia;
use App\Models\Reserva;
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
        
        $validated = $request->validate([
            'tipo' => 'required|in:problema_posuso,dano,otro',
            'descripcion' => 'required|string|min:10|max:1000',
        ], [
            'tipo.required' => 'Debe seleccionar un tipo de incidencia',
            'tipo.in' => 'El tipo de incidencia seleccionado no es válido',
            'descripcion.required' => 'La descripción de la incidencia es obligatoria',
            'descripcion.min' => 'La descripción debe tener al menos 10 caracteres',
            'descripcion.max' => 'La descripción no puede exceder 1000 caracteres',
        ]);
        
        $incidencia = Incidencia::create([
            'reserva_id' => $reserva->id,
            'tipo' => $validated['tipo'],
            'descripcion' => $validated['descripcion'],
            'estado' => 'reportada',
        ]);
        
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
        
        $incidencia->update([
            'estado' => $validated['estado'],
        ]);
        
        return redirect()->back()
            ->with('success', 'Estado de la incidencia actualizado correctamente.');
    }

    /**
     * Eliminar incidencia
     */
    public function destroy(Incidencia $incidencia)
    {
        $reservaId = $incidencia->reserva_id;
        $incidencia->delete();
        
        return redirect()->route('admin.incidencias.crear', $reservaId)
            ->with('success', 'Incidencia eliminada correctamente.');
    }
}