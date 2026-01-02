<?php

namespace App\Http\Controllers;

use App\Models\Recinto;
use App\Models\Reserva;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Rules\ValidRut;
use App\Rules\ValidTelefonoChileno;
use Carbon\Carbon;

class ReservaController extends Controller
{
    public function create(Recinto $recinto)
    {
        // Calcular fecha máxima (60 días desde hoy)
        $fechaMaxima = Carbon::today()->addDays(60)->format('Y-m-d');
        
        // ✅ NO VERIFICAR AQUÍ - Solo pasar variable
        $restriccion = ['restringido' => false];
        
        return view('reservas.create', compact('recinto', 'fechaMaxima', 'restriccion'));
    }
    
    /**
     * ✅ NUEVA FUNCIÓN: Verificar si hay restricción por cancelaciones
     * Ahora verifica por NOMBRE DE ORGANIZACIÓN (en cualquier recinto)
     */
    private function verificarRestriccionCancelaciones($nombreOrganizacion)
    {
        // Obtener todas las reservas de esa organización que fueron aprobadas y luego canceladas
        $mesActual = now()->month;
        $anoActual = now()->year;
        
        // ✅ CAMBIO: Buscar por nombre_organizacion en lugar de recinto_id
        $cancelacionesEsteMes = Reserva::where('nombre_organizacion', $nombreOrganizacion)
            ->where('estado', 'aprobada')
            ->whereNotNull('fecha_cancelacion')
            ->whereMonth('fecha_cancelacion', $mesActual)
            ->whereYear('fecha_cancelacion', $anoActual)
            ->count();
        
        if ($cancelacionesEsteMes >= 3) {
            $proximoMes = Carbon::now()->addMonth();
            return [
                'restringido' => true,
                'cancelaciones' => $cancelacionesEsteMes,
                'proximoMes' => $proximoMes->format('F'),
                'fechaDesbloqueo' => $proximoMes->format('d/m/Y')
            ];
        }
        
        return [
            'restringido' => false,
            'cancelaciones' => $cancelacionesEsteMes
        ];
    }
    
    public function store(Request $request)
    {
        // Calcular fecha máxima para validación
        $fechaMaxima = Carbon::today()->addDays(60);
        
        // Validar primero para tener el nombre de la organización
        $validated = $request->validate([
            'recinto_id' => 'required|exists:recintos,id',
            'deporte' => 'required|string|max:50',
            'rut' => ['required', 'string', 'max:12', new ValidRut],
            'nombre_organizacion' => 'required|string|max:255',
            'representante_nombre' => 'required|string|max:255',
            'email' => 'required|email',
            'email_confirmacion' => 'required|email|same:email',
            'telefono' => ['nullable', 'string', 'max:20', new ValidTelefonoChileno],
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
            // MENSAJES PERSONALIZADOS EN ESPAÑOL
            
            // Recinto
            'recinto_id.required' => 'Debe seleccionar un recinto.',
            'recinto_id.exists' => 'El recinto seleccionado no es válido.',
            
            // Deporte
            'deporte.required' => 'El deporte es obligatorio.',
            'deporte.string' => 'El deporte debe ser texto.',
            'deporte.max' => 'El deporte no puede tener más de 50 caracteres.',
            
            // RUT
            'rut.required' => 'El RUT del representante es obligatorio.',
            'rut.string' => 'El RUT debe ser texto.',
            'rut.max' => 'El RUT no puede tener más de 12 caracteres.',
            
            // Nombre Organización
            'nombre_organizacion.required' => 'El nombre del club u organización es obligatorio.',
            'nombre_organizacion.string' => 'El nombre del club u organización debe ser texto.',
            'nombre_organizacion.max' => 'El nombre del club u organización no puede tener más de 255 caracteres.',
            
            // Nombre Representante
            'representante_nombre.required' => 'El nombre del representante es obligatorio.',
            'representante_nombre.string' => 'El nombre del representante debe ser texto.',
            'representante_nombre.max' => 'El nombre del representante no puede tener más de 255 caracteres.',
            
            // Email
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe tener un formato válido.',
            
            // Email Confirmación
            'email_confirmacion.required' => 'La confirmación del correo electrónico es obligatoria.',
            'email_confirmacion.email' => 'La confirmación del correo electrónico debe tener un formato válido.',
            'email_confirmacion.same' => 'Los correos electrónicos no coinciden.',
            
            // Teléfono
            'telefono.string' => 'El teléfono debe ser texto.',
            'telefono.max' => 'El teléfono no puede tener más de 20 caracteres.',
            
            // Dirección
            'direccion.string' => 'La dirección debe ser texto.',
            'direccion.max' => 'La dirección no puede tener más de 500 caracteres.',
            
            // Región
            'region.string' => 'La región debe ser texto.',
            'region.max' => 'La región no puede tener más de 100 caracteres.',
            
            // Comuna
            'comuna.string' => 'La comuna debe ser texto.',
            'comuna.max' => 'La comuna no puede tener más de 100 caracteres.',
            
            // Cantidad de Personas
            'cantidad_personas.required' => 'La cantidad de personas es obligatoria.',
            'cantidad_personas.integer' => 'La cantidad de personas debe ser un número entero.',
            'cantidad_personas.min' => 'La cantidad de personas debe ser al menos 1.',
            'cantidad_personas.max' => 'La cantidad de personas no puede ser mayor a 500.',
            
            // Fecha de Reserva
            'fecha_reserva.required' => 'La fecha de reserva es obligatoria.',
            'fecha_reserva.date' => 'La fecha de reserva debe ser una fecha válida.',
            'fecha_reserva.after' => 'La fecha de reserva debe ser posterior a hoy.',
            'fecha_reserva.before_or_equal' => 'La fecha de reserva no puede ser mayor a 60 días desde hoy.',
            
            // Hora Inicio
            'hora_inicio.required' => 'La hora de inicio es obligatoria.',
            'hora_inicio.date_format' => 'La hora de inicio debe tener el formato HH:MM.',
            
            // Hora Fin
            'hora_fin.required' => 'La hora de término es obligatoria.',
            'hora_fin.date_format' => 'La hora de término debe tener el formato HH:MM.',
            'hora_fin.after' => 'La hora de término debe ser posterior a la hora de inicio.',
            
            // Observaciones
            'observaciones.string' => 'Las observaciones deben ser texto.',
            'observaciones.max' => 'Las observaciones no pueden tener más de 1000 caracteres.',
            
            // Aceptación del Reglamento
            'acepta_reglamento.required' => 'Debe aceptar el reglamento para continuar.',
            'acepta_reglamento.accepted' => 'Debe aceptar el reglamento para continuar.',
        ]);
        
        // ✅ VERIFICAR RESTRICCIÓN USANDO EL NOMBRE DE LA ORGANIZACIÓN
        $restriccion = $this->verificarRestriccionCancelaciones($validated['nombre_organizacion']);
        
        if ($restriccion['restringido']) {
            return back()
                ->withInput()
                ->withErrors(['restriccion' => 'Su organización ha alcanzado el límite de 3 cancelaciones permitidas en este mes. Podrá hacer nuevas reservas a partir del ' . $restriccion['fechaDesbloqueo'] . '.']);
        }
        
        // Verificar disponibilidad
        $recinto = Recinto::find($validated['recinto_id']);
        if (!$recinto->disponibleEn($validated['fecha_reserva'], $validated['hora_inicio'], $validated['hora_fin'])) {
            return back()
                ->withInput()
                ->withErrors(['horario' => 'El horario seleccionado no está disponible para la fecha indicada.']);
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