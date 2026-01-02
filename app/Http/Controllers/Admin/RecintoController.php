<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Recinto;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RecintoController extends Controller
{
    /**
     * Mostrar listado de recintos / segunda etapa pendiente.
     */
    public function index()
    {
        $recintos = Recinto::orderBy('nombre')->paginate(10);
        
        return view('admin.recintos.index', compact('recintos'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        return view('admin.recintos.create');
    }

    /**
     * Guardar nuevo recinto
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:recintos,nombre',
            'descripcion' => 'nullable|string|max:500',
            'capacidad_maxima' => 'required|integer|min:1',
            'activo' => 'required|boolean',
            'horario_inicio' => 'required|date_format:H:i',
            'horario_fin' => 'required|date_format:H:i|after:horario_inicio',
            'dias_cerrados_completos' => 'nullable|array',
            'dias_cerrados_completos.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'bloqueos' => 'nullable|array',
            'bloqueos.*.fecha' => 'required|date|after_or_equal:today',
            'bloqueos.*.hora_inicio' => 'required|date_format:H:i',
            'bloqueos.*.hora_fin' => 'required|date_format:H:i|after:bloqueos.*.hora_inicio',
            'bloqueos.*.motivo' => 'nullable|string|max:100',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'nombre.required' => 'El nombre del recinto es obligatorio',
            'nombre.unique' => 'Ya existe un recinto con este nombre',
            'capacidad_maxima.required' => 'La capacidad máxima es obligatoria',
            'capacidad_maxima.min' => 'La capacidad debe ser al menos 1 persona',
            'horario_inicio.required' => 'El horario de inicio es obligatorio',
            'horario_fin.required' => 'El horario de fin es obligatorio',
            'horario_fin.after' => 'El horario de fin debe ser después del horario de inicio',
            'activo.required' => 'El estado es obligatorio',
            'bloqueos.*.fecha.after_or_equal' => 'La fecha del bloqueo debe ser hoy o posterior',
            'imagen.image' => 'El archivo debe ser una imagen',
            'imagen.mimes' => 'La imagen debe ser formato: jpeg, png, jpg',
            'imagen.max' => 'La imagen no debe pesar más de 2MB',
        ]);

        // Preparar horarios en formato JSON
        $horarios = [
            'inicio' => $validated['horario_inicio'],
            'fin' => $validated['horario_fin']
        ];

        //  PREPARAR ESTRUCTURA CON FECHAS ESPECÍFICAS 
        $diasCerrados = [
            'dias_completos' => $validated['dias_cerrados_completos'] ?? [],
            'rangos_bloqueados' => []
        ];

        // Procesar bloqueos de horarios con fechas específicas
        if ($request->has('bloqueos') && is_array($request->bloqueos)) {
            foreach ($request->bloqueos as $bloqueo) {
                if (isset($bloqueo['fecha']) && isset($bloqueo['hora_inicio']) && isset($bloqueo['hora_fin'])) {
                    $diasCerrados['rangos_bloqueados'][] = [
                        'fecha' => $bloqueo['fecha'], //  FECHA ESPECÍFICA 
                        'hora_inicio' => $bloqueo['hora_inicio'],
                        'hora_fin' => $bloqueo['hora_fin'],
                        'motivo' => $bloqueo['motivo'] ?? ''
                    ];
                }
            }
        }

        // Crear el recinto
        $recinto = new Recinto();
        $recinto->nombre = $validated['nombre'];
        $recinto->descripcion = $validated['descripcion'] ?? null;
        $recinto->capacidad_maxima = $validated['capacidad_maxima'];
        $recinto->activo = $validated['activo'];
        $recinto->horarios_disponibles = $horarios;
        $recinto->dias_cerrados = $diasCerrados;

        // Manejar la imagen si existe
        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('recintos', 'public');
            $recinto->imagen_url = $path;
        }

        $recinto->save();

        // Registrar en auditoría
        AuditLog::log(
            'crear_recinto',
            "Recinto '{$recinto->nombre}' creado con capacidad de {$recinto->capacidad_maxima} personas",
            $recinto,
            null,
            [
                'nombre' => $recinto->nombre,
                'capacidad_maxima' => $recinto->capacidad_maxima,
                'activo' => $recinto->activo,
            ]
        );

        return redirect()->route('admin.recintos.index')
            ->with('success', 'Recinto creado exitosamente');
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(Recinto $recinto)
    {
        // Decodificar horarios
        $horarios = is_string($recinto->horarios_disponibles) 
            ? json_decode($recinto->horarios_disponibles, true) 
            : $recinto->horarios_disponibles;

        // Decodificar días cerrados
        $diasCerrados = is_string($recinto->dias_cerrados) 
            ? json_decode($recinto->dias_cerrados, true) 
            : ($recinto->dias_cerrados ?? []);

        return view('admin.recintos.edit', compact('recinto', 'horarios', 'diasCerrados'));
    }

    /**
     *  ACTUALIZADO: Actualizar recinto con fechas específicas 
     */
    public function update(Request $request, Recinto $recinto)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:recintos,nombre,' . $recinto->id,
            'descripcion' => 'nullable|string|max:500',
            'capacidad_maxima' => 'required|integer|min:1',
            'activo' => 'required|boolean',
            'horario_inicio' => 'required|date_format:H:i',
            'horario_fin' => 'required|date_format:H:i|after:horario_inicio',
            'dias_cerrados_completos' => 'nullable|array',
            'dias_cerrados_completos.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'bloqueos' => 'nullable|array',
            'bloqueos.*.fecha' => 'required|date|after_or_equal:today',
            'bloqueos.*.hora_inicio' => 'required|date_format:H:i',
            'bloqueos.*.hora_fin' => 'required|date_format:H:i',
            'bloqueos.*.motivo' => 'nullable|string|max:100',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'eliminar_imagen' => 'nullable|boolean',
        ], [
            'nombre.required' => 'El nombre del recinto es obligatorio',
            'nombre.unique' => 'Ya existe un recinto con este nombre',
            'capacidad_maxima.required' => 'La capacidad máxima es obligatoria',
            'capacidad_maxima.min' => 'La capacidad debe ser al menos 1 persona',
            'horario_inicio.required' => 'El horario de inicio es obligatorio',
            'horario_fin.required' => 'El horario de fin es obligatorio',
            'horario_fin.after' => 'El horario de fin debe ser después del horario de inicio',
            'activo.required' => 'El estado es obligatorio',
            'bloqueos.*.fecha.after_or_equal' => 'La fecha del bloqueo debe ser hoy o posterior',
            'imagen.image' => 'El archivo debe ser una imagen',
            'imagen.mimes' => 'La imagen debe ser formato: jpeg, png, jpg',
            'imagen.max' => 'La imagen no debe pesar más de 2MB',
        ]);

        // Guardar valores originales antes de actualizar
        $valoresOriginales = [
            'nombre' => $recinto->nombre,
            'capacidad_maxima' => $recinto->capacidad_maxima,
            'activo' => $recinto->activo,
        ];

        // Preparar horarios en formato array
        $horarios = [
            'inicio' => $validated['horario_inicio'],
            'fin' => $validated['horario_fin']
        ];

        //  PREPARAR ESTRUCTURA CON FECHAS ESPECÍFICAS 
        $diasCerrados = [
            'dias_completos' => $validated['dias_cerrados_completos'] ?? [],
            'rangos_bloqueados' => []
        ];

        // Procesar bloqueos de horarios con fechas específicas
        if ($request->has('bloqueos') && is_array($request->bloqueos)) {
            foreach ($request->bloqueos as $bloqueo) {
                if (isset($bloqueo['fecha']) && isset($bloqueo['hora_inicio']) && isset($bloqueo['hora_fin'])) {
                    $diasCerrados['rangos_bloqueados'][] = [
                        'fecha' => $bloqueo['fecha'], //  FECHA ESPECÍFICA ESTO ES NUEVO
                        'hora_inicio' => $bloqueo['hora_inicio'],
                        'hora_fin' => $bloqueo['hora_fin'],
                        'motivo' => $bloqueo['motivo'] ?? ''
                    ];
                }
            }
        }

        // Actualizar campos
        $recinto->nombre = $validated['nombre'];
        $recinto->descripcion = $validated['descripcion'] ?? null;
        $recinto->capacidad_maxima = $validated['capacidad_maxima'];
        $recinto->activo = $validated['activo'];
        $recinto->horarios_disponibles = $horarios;
        $recinto->dias_cerrados = $diasCerrados; // ← ESTRUCTURA ACTUALIZADA

        // Eliminar imagen si se solicita
        if ($request->has('eliminar_imagen') && $request->eliminar_imagen) {
            if ($recinto->imagen_url) {
                Storage::disk('public')->delete($recinto->imagen_url);
                $recinto->imagen_url = null;
            }
        }

        // Manejar nueva imagen
        if ($request->hasFile('imagen')) {
            // Eliminar imagen anterior si existe
            if ($recinto->imagen_url) {
                Storage::disk('public')->delete($recinto->imagen_url);
            }
            
            $path = $request->file('imagen')->store('recintos', 'public');
            $recinto->imagen_url = $path;
        }

        $recinto->save();

        // Registrar en auditoría
        AuditLog::log(
            'editar_recinto',
            "Recinto '{$recinto->nombre}' actualizado",
            $recinto,
            $valoresOriginales,
            [
                'nombre' => $recinto->nombre,
                'capacidad_maxima' => $recinto->capacidad_maxima,
                'activo' => $recinto->activo,
            ]
        );

        return redirect()->route('admin.recintos.index')
            ->with('success', 'Recinto actualizado exitosamente');
    }

    /**
     * Eliminar recinto
     */
    public function destroy(Recinto $recinto)
    {
        // Verificar si tiene reservas asociadas
        $tieneReservas = $recinto->reservas()->exists();

        if ($tieneReservas) {
            return redirect()->route('admin.recintos.index')
                ->with('error', 'No se puede eliminar el recinto porque tiene reservas asociadas. Desactívalo en su lugar.');
        }

        // Guardar nombre antes de eliminar
        $nombre = $recinto->nombre;
        $capacidad = $recinto->capacidad_maxima;

        // Registrar en auditoría antes de eliminar
        AuditLog::log(
            'eliminar_recinto',
            "Recinto '{$nombre}' eliminado (capacidad: {$capacidad})",
            $recinto,
            [
                'nombre' => $recinto->nombre,
                'capacidad_maxima' => $recinto->capacidad_maxima,
                'activo' => $recinto->activo,
            ],
            null
        );

        // Eliminar imagen si existe
        if ($recinto->imagen_url) {
            Storage::disk('public')->delete($recinto->imagen_url);
        }

        $recinto->delete();

        return redirect()->route('admin.recintos.index')
            ->with('success', 'Recinto eliminado exitosamente');
    }

    /**
     * Cambiar estado del recinto
     */
    public function cambiarEstado(Request $request, Recinto $recinto)
    {
        $validated = $request->validate([
            'activo' => 'required|boolean',
        ]);

        // Guardar estado original
        $estadoOriginal = $recinto->activo;

        $recinto->activo = $validated['activo'];
        $recinto->save();

        // Registrar en auditoría
        $accion = $validated['activo'] ? 'activar_recinto' : 'desactivar_recinto';
        $descripcion = $validated['activo'] 
            ? "Recinto '{$recinto->nombre}' activado" 
            : "Recinto '{$recinto->nombre}' desactivado";

        AuditLog::log(
            $accion,
            $descripcion,
            $recinto,
            ['activo' => $estadoOriginal],
            ['activo' => $validated['activo']]
        );

        return redirect()->route('admin.recintos.index')
            ->with('success', 'Estado del recinto actualizado exitosamente');
    }
}