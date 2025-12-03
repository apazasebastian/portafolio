<?php

namespace App\Http\Controllers;

use App\Models\Recinto;
use App\Models\Reserva;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Rules\ValidRut;

class ReservaController extends Controller
{
    public function create(Recinto $recinto)
    {
        return view('reservas.create', compact('recinto'));
    }
    
    public function store(Request $request)
{
    $validated = $request->validate([
        'recinto_id' => 'required|exists:recintos,id',
        'deporte' => 'required|string|max:50',
        'rut' => ['required', 'string', 'max:12', new ValidRut], // ← LÍNEA MODIFICADA * No esta pendiente
        'nombre_organizacion' => 'required|string|max:255',
        'representante_nombre' => 'required|string|max:255',
        'email' => 'required|email',
        'email_confirmacion' => 'required|email|same:email',
        'telefono' => 'nullable|string|max:20',
        'direccion' => 'nullable|string|max:500',
        'region' => 'nullable|string|max:100',
        'comuna' => 'nullable|string|max:100',
        'cantidad_personas' => 'required|integer|min:1|max:500',
        'fecha_reserva' => 'required|date|after:today',
        'hora_inicio' => 'required|date_format:H:i',
        'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
        'observaciones' => 'nullable|string|max:1000',
        'acepta_reglamento' => 'required|accepted'
    ]);
    
    // Verificar disponibilidad
    $recinto = Recinto::find($validated['recinto_id']);
    if (!$recinto->disponibleEn($validated['fecha_reserva'], $validated['hora_inicio'], $validated['hora_fin'])) {
        return back()->withErrors(['horario' => 'El horario seleccionado no está disponible']);
    }
    
    // Crear la reserva
    $reserva = Reserva::create($validated);
    
    return redirect()->route('calendario')
        ->with('success', 'Reserva enviada exitosamente. Recibirá una confirmación por correo electrónico.');
    }
}

