<?php

namespace App\Http\Controllers;

use App\Models\Recinto;
use App\Models\Reserva;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Rules\ValidRut;
use App\Rules\ValidTelefonoChileno;
use Carbon\Carbon;

/**
 * Controlador de Reservas
 * 
 * Maneja todo el proceso de creacion de nuevas reservas por parte
 * de los ciudadanos: mostrar el formulario, validar datos, verificar
 * disponibilidad y guardar la reserva en el sistema.
 */
class ReservaController extends Controller
{
    /**
     * Muestra el formulario para crear una nueva reserva
     * 
     * Cuando un ciudadano hace clic en "Reservar" desde el calendario,
     * esta funcion le muestra el formulario donde debe ingresar los
     * datos de su organizacion y seleccionar fecha/horario.
     */
    public function create(Recinto $recinto)
    {
        // Las reservas solo se pueden hacer hasta 60 dias en el futuro
        $fechaMaxima = Carbon::today()->addDays(60)->format('Y-m-d');
        
        // Inicializamos sin restriccion, la verificacion se hace al enviar
        $restriccion = ['restringido' => false];
        
        return view('reservas.create', compact('recinto', 'fechaMaxima', 'restriccion'));
    }
    
    /**
     * Verifica si un representante puede hacer reservas
     * 
     * Si una persona cancela mas de 3 reservas en un mes, se le bloquea
     * la posibilidad de hacer nuevas reservas hasta el mes siguiente.
     * Esto se hace para evitar el abuso del sistema.
     * 
     * La verificacion se hace por RUT del representante, sin importar
     * en que recinto haya hecho las cancelaciones.
     */
    private function verificarRestriccionCancelaciones($rutRepresentante)
    {
        $mesActual = now()->month;
        $anoActual = now()->year;
        
        // Cuenta cuantas reservas aprobadas fueron canceladas este mes por este RUT
        $cancelacionesEsteMes = Reserva::where('rut', $rutRepresentante)
            ->where('estado', 'aprobada')
            ->whereNotNull('fecha_cancelacion')
            ->whereMonth('fecha_cancelacion', $mesActual)
            ->whereYear('fecha_cancelacion', $anoActual)
            ->count();
        
        // Si tiene 3 o mas cancelaciones, esta restringido
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
    
    /**
     * Procesa y guarda una nueva solicitud de reserva
     * 
     * Cuando el ciudadano envia el formulario de reserva, esta funcion:
     * 1. Valida que todos los datos esten correctos
     * 2. Verifica que el solicitante no tenga restricciones por cancelaciones
     * 3. Confirma que el horario siga disponible
     * 4. Guarda la reserva en estado "pendiente" para revision del administrador
     */
    public function store(Request $request)
    {
        // Las reservas solo se pueden hacer hasta 60 dias en el futuro
        $fechaMaxima = Carbon::today()->addDays(60);
        
        // Valida todos los campos del formulario
        // Si hay errores, Laravel automaticamente regresa al formulario con los mensajes
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
            // Mensajes de error en español para cada campo
            
            'recinto_id.required' => 'Debe seleccionar un recinto.',
            'recinto_id.exists' => 'El recinto seleccionado no es válido.',
            
            'deporte.required' => 'El deporte es obligatorio.',
            'deporte.string' => 'El deporte debe ser texto.',
            'deporte.max' => 'El deporte no puede tener más de 50 caracteres.',
            
            'rut.required' => 'El RUT del representante es obligatorio.',
            'rut.string' => 'El RUT debe ser texto.',
            'rut.max' => 'El RUT no puede tener más de 12 caracteres.',
            
            'nombre_organizacion.required' => 'El nombre del club u organización es obligatorio.',
            'nombre_organizacion.string' => 'El nombre del club u organización debe ser texto.',
            'nombre_organizacion.max' => 'El nombre del club u organización no puede tener más de 255 caracteres.',
            
            'representante_nombre.required' => 'El nombre del representante es obligatorio.',
            'representante_nombre.string' => 'El nombre del representante debe ser texto.',
            'representante_nombre.max' => 'El nombre del representante no puede tener más de 255 caracteres.',
            
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe tener un formato válido.',
            
            'email_confirmacion.required' => 'La confirmación del correo electrónico es obligatoria.',
            'email_confirmacion.email' => 'La confirmación del correo electrónico debe tener un formato válido.',
            'email_confirmacion.same' => 'Los correos electrónicos no coinciden.',
            
            'telefono.string' => 'El teléfono debe ser texto.',
            'telefono.max' => 'El teléfono no puede tener más de 20 caracteres.',
            
            'direccion.string' => 'La dirección debe ser texto.',
            'direccion.max' => 'La dirección no puede tener más de 500 caracteres.',
            
            'region.string' => 'La región debe ser texto.',
            'region.max' => 'La región no puede tener más de 100 caracteres.',
            
            'comuna.string' => 'La comuna debe ser texto.',
            'comuna.max' => 'La comuna no puede tener más de 100 caracteres.',
            
            'cantidad_personas.required' => 'La cantidad de personas es obligatoria.',
            'cantidad_personas.integer' => 'La cantidad de personas debe ser un número entero.',
            'cantidad_personas.min' => 'La cantidad de personas debe ser al menos 1.',
            'cantidad_personas.max' => 'La cantidad de personas no puede ser mayor a 500.',
            
            'fecha_reserva.required' => 'La fecha de reserva es obligatoria.',
            'fecha_reserva.date' => 'La fecha de reserva debe ser una fecha válida.',
            'fecha_reserva.after' => 'La fecha de reserva debe ser posterior a hoy.',
            'fecha_reserva.before_or_equal' => 'La fecha de reserva no puede ser mayor a 60 días desde hoy.',
            
            'hora_inicio.required' => 'La hora de inicio es obligatoria.',
            'hora_inicio.date_format' => 'La hora de inicio debe tener el formato HH:MM.',
            
            'hora_fin.required' => 'La hora de término es obligatoria.',
            'hora_fin.date_format' => 'La hora de término debe tener el formato HH:MM.',
            'hora_fin.after' => 'La hora de término debe ser posterior a la hora de inicio.',
            
            'observaciones.string' => 'Las observaciones deben ser texto.',
            'observaciones.max' => 'Las observaciones no pueden tener más de 1000 caracteres.',
            
            'acepta_reglamento.required' => 'Debe aceptar el reglamento para continuar.',
            'acepta_reglamento.accepted' => 'Debe aceptar el reglamento para continuar.',
        ]);
        
        // Verifica si el representante tiene restriccion por demasiadas cancelaciones
        $restriccion = $this->verificarRestriccionCancelaciones($validated['rut']);
        
        if ($restriccion['restringido']) {
            return back()
                ->withInput()
                ->withErrors(['restriccion' => 'Ha alcanzado el límite de 3 cancelaciones permitidas en este mes para este RUT. Podrá hacer nuevas reservas a partir del ' . $restriccion['fechaDesbloqueo'] . '.']);
        }
        
        // Verifica que el horario solicitado este realmente disponible
        // (pudo haber sido reservado por otra persona mientras llenaba el formulario)
        $recinto = Recinto::find($validated['recinto_id']);
        if (!$recinto->disponibleEn($validated['fecha_reserva'], $validated['hora_inicio'], $validated['hora_fin'])) {
            return back()
                ->withInput()
                ->withErrors(['horario' => 'El horario seleccionado no está disponible para la fecha indicada.']);
        }
        
        // Guarda la reserva en la base de datos (queda en estado "pendiente")
        $reserva = Reserva::create($validated);
        
        // Redirige al calendario con un mensaje de exito
        return redirect()->route('calendario')
            ->with('success', 'Reserva enviada exitosamente. Recibirá una confirmación por correo electrónico.');
    }
    
    /**
     * Muestra los detalles de una reserva especifica
     * 
     * Esta pagina muestra toda la informacion de una reserva,
     * incluyendo los datos de la organizacion y el recinto reservado.
     */
    public function show(Reserva $reserva)
    {
        // Carga la informacion del recinto junto con la reserva
        $reserva->load('recinto');
        return view('reservas.show', compact('reserva'));
    }
}
