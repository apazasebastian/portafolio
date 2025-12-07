<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Evento;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventoController extends Controller
{
    /**
     * Mostrar listado de eventos
     */
    public function index()
    {
        $eventos = Evento::orderBy('fecha_evento', 'desc')->paginate(10);
        
        return view('admin.eventos.index', compact('eventos'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        return view('admin.eventos.create');
    }

    /**
     * Guardar nuevo evento
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string|max:500',
            'fecha_evento' => 'required|date',
            'enlace_externo' => 'nullable|url',
            'imagen' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'activo' => 'required|boolean',
        ], [
            'titulo.required' => 'El título del evento es obligatorio',
            'descripcion.required' => 'La descripción es obligatoria',
            'fecha_evento.required' => 'La fecha del evento es obligatoria',
            'enlace_externo.url' => 'El enlace debe ser una URL válida',
            'imagen.required' => 'La imagen es obligatoria',
            'imagen.image' => 'El archivo debe ser una imagen',
            'imagen.mimes' => 'La imagen debe ser formato: jpeg, png, jpg',
            'imagen.max' => 'La imagen no debe pesar más de 2MB',
        ]);

        $evento = new Evento();
        $evento->titulo = $validated['titulo'];
        $evento->descripcion = $validated['descripcion'];
        $evento->fecha_evento = $validated['fecha_evento'];
        $evento->enlace_externo = $validated['enlace_externo'] ?? null;
        $evento->activo = $validated['activo'];

        // Guardar imagen
        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('eventos', 'public');
            $evento->imagen_url = $path;
        }

        $evento->save();

        // ⚠️ REGISTRAR EN AUDITORÍA ⚠️
        AuditLog::log(
            'crear_evento',
            "Evento '{$evento->titulo}' creado para el " . \Carbon\Carbon::parse($evento->fecha_evento)->format('d/m/Y'),
            $evento,
            null,
            [
                'titulo' => $evento->titulo,
                'fecha_evento' => $evento->fecha_evento,
                'activo' => $evento->activo,
            ]
        );

        return redirect()->route('admin.eventos.index')
            ->with('success', 'Evento creado exitosamente');
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(Evento $evento)
    {
        return view('admin.eventos.edit', compact('evento'));
    }

    /**
     * Actualizar evento
     */
    public function update(Request $request, Evento $evento)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string|max:500',
            'fecha_evento' => 'required|date',
            'enlace_externo' => 'nullable|url',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'activo' => 'required|boolean',
            'eliminar_imagen' => 'nullable|boolean',
        ], [
            'titulo.required' => 'El título del evento es obligatorio',
            'descripcion.required' => 'La descripción es obligatoria',
            'fecha_evento.required' => 'La fecha del evento es obligatoria',
            'enlace_externo.url' => 'El enlace debe ser una URL válida',
            'imagen.image' => 'El archivo debe ser una imagen',
            'imagen.mimes' => 'La imagen debe ser formato: jpeg, png, jpg',
            'imagen.max' => 'La imagen no debe pesar más de 2MB',
        ]);

        // ⚠️ GUARDAR VALORES ORIGINALES ANTES DE ACTUALIZAR ⚠️
        $valoresOriginales = [
            'titulo' => $evento->titulo,
            'fecha_evento' => $evento->fecha_evento,
            'activo' => $evento->activo,
        ];

        $evento->titulo = $validated['titulo'];
        $evento->descripcion = $validated['descripcion'];
        $evento->fecha_evento = $validated['fecha_evento'];
        $evento->enlace_externo = $validated['enlace_externo'] ?? null;
        $evento->activo = $validated['activo'];

        // Eliminar imagen si se solicita
        if ($request->has('eliminar_imagen') && $request->eliminar_imagen) {
            if ($evento->imagen_url) {
                Storage::disk('public')->delete($evento->imagen_url);
                $evento->imagen_url = null;
            }
        }

        // Actualizar imagen
        if ($request->hasFile('imagen')) {
            // Eliminar imagen anterior
            if ($evento->imagen_url) {
                Storage::disk('public')->delete($evento->imagen_url);
            }
            
            $path = $request->file('imagen')->store('eventos', 'public');
            $evento->imagen_url = $path;
        }

        $evento->save();

        // ⚠️ REGISTRAR EN AUDITORÍA ⚠️
        AuditLog::log(
            'editar_evento',
            "Evento '{$evento->titulo}' actualizado",
            $evento,
            $valoresOriginales,
            [
                'titulo' => $evento->titulo,
                'fecha_evento' => $evento->fecha_evento,
                'activo' => $evento->activo,
            ]
        );

        return redirect()->route('admin.eventos.index')
            ->with('success', 'Evento actualizado exitosamente');
    }

    /**
     * Eliminar evento
     */
    public function destroy(Evento $evento)
    {
        // ⚠️ GUARDAR TÍTULO ANTES DE ELIMINAR ⚠️
        $titulo = $evento->titulo;
        $fecha = $evento->fecha_evento;

        // ⚠️ REGISTRAR EN AUDITORÍA ANTES DE ELIMINAR ⚠️
        AuditLog::log(
            'eliminar_evento',
            "Evento '{$titulo}' eliminado",
            $evento,
            [
                'titulo' => $evento->titulo,
                'fecha_evento' => $evento->fecha_evento,
            ],
            null
        );

        // Eliminar imagen si existe
        if ($evento->imagen_url) {
            Storage::disk('public')->delete($evento->imagen_url);
        }

        $evento->delete();

        return redirect()->route('admin.eventos.index')
            ->with('success', 'Evento eliminado exitosamente');
    }
}