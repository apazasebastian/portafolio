<?php

namespace App\Http\Controllers;

use App\Models\Recinto;
use App\Models\Reserva;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Rules\ValidRut;
use Carbon\Carbon;

class ReservaController extends Controller
{
    public function create(Recinto $recinto)
    {
        // Calcular fecha máxima (60 días desde hoy)
        $fechaMaxima = Carbon::today()->addDays(60)->format('Y-m-d');
        
        return view('reservas.create', compact('recinto', 'fechaMaxima'));
    }
    
    public function store(Request $request)
    {
        // Calcular fecha máxima (60 días desde hoy)
        $fechaMaxima = Carbon::today()->addDays(60);
        
        $validated = $request->validate([
            'recinto_id' => 'required|exists:recintos,id',
            'deporte' => 'required|string|max:50',
            'rut' => ['required', 'string', 'max:12', new ValidRut],
            'nombre_organizacion' => 'required|string|max:255',
            'representante_nombre' => 'required|string|max:255',
            'email' => 'required|email',
            'email_confirmacion' => 'required|email|same:email',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:500',
            'region' => 'nullable|string|max:100',
            'comuna' => 'nullable|string|max:100',
            'cantidad_personas' => 'required|integer|min:1|max:500',
            'fecha_reserva' => [
                'required',
                'date',
                'after:today',
                'before_or_equal:' . $fechaMaxima->format('Y-m-d')
            ],
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'observaciones' => 'nullable|string|max:1000',
            'acepta_reglamento' => 'required|accepted'
        ], [
            // Mensajes personalizados
            'fecha_reserva.after' => 'La fecha de reserva debe ser posterior a hoy.',
            'fecha_reserva.before_or_equal' => 'La fecha de reserva no puede ser mayor a 60 días desde hoy.',
        ]);
        
        // Verificar disponibilidad
        $recinto = Recinto::find($validated['recinto_id']);
        if (!$recinto->disponibleEn($validated['fecha_reserva'], $validated['hora_inicio'], $validated['hora_fin'])) {
            return back()->withErrors(['horario' => 'El horario seleccionado no está disponible'])->withInput();
        }
        
        // Crear la reserva
        $reserva = Reserva::create($validated);
        
        return redirect()->route('calendario')
            ->with('success', 'Reserva enviada exitosamente. Recibirá una confirmación por correo electrónico.');
    }

    public function show(Reserva $reserva)
    {
        $reserva->load('recinto');
        return view('reservas.show', compact('reserva'));
    }
}