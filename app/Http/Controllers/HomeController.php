<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\Recinto;
use App\Models\User;
use App\Services\DisponibilidadService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct(
        private DisponibilidadService $disponibilidadService
    ) {}

    public function index()
    {
        // Obtener eventos activos ordenados por fecha
        $eventos = Evento::where('activo', true)
            ->where('fecha_evento', '>=', now())
            ->orderBy('fecha_evento', 'asc')
            ->take(5)
            ->get();

        // Obtener todos los recintos activos
        $recintos = Recinto::activos()->get();

        // Obtener emails de encargados desde la base de datos
        $encargadoEmails = [];
        $encargados = User::where('role', 'encargado_recinto')
                          ->where('activo', 1)
                          ->whereNotNull('recinto_asignado_id')
                          ->get();

        foreach ($encargados as $encargado) {
            $encargadoEmails[$encargado->recinto_asignado_id] = $encargado->email;
        }

        // Direcciones de recintos para Google Maps (mismo formato que el calendario)
        $direccionesRecintos = [
            1 => 'Pablo Picasso 2150, Arica, Chile',  // Epicentro 1
            2 => 'Ginebra 3708, Arica, Chile',        // Epicentro 2
            3 => 'Rafael Sotomayor 600, Arica, Chile', // Fortín Sotomayor
            4 => 'España 121, Arica, Chile',          // Piscina Olímpica
        ];

        // Obtener el mes actual y el siguiente
        $mesActual = Carbon::now()->startOfMonth();
        $mesSiguiente = Carbon::now()->addMonth()->startOfMonth();

        // Generar días del mes actual
        $diasMesActual = $this->generarDiasMes($mesActual);

        // Generar días del mes siguiente
        $diasMesSiguiente = $this->generarDiasMes($mesSiguiente);

        return view('home.index', compact(
            'eventos',
            'recintos',
            'encargadoEmails',
            'direccionesRecintos',
            'mesActual',
            'mesSiguiente',
            'diasMesActual',
            'diasMesSiguiente'
        ));
    }

    /**
     * API de disponibilidad con fechas específicas
     * Ahora usa el DisponibilidadService para evitar duplicación
     */
    public function obtenerDisponibilidad(Request $request)
    {
        $recintoId = $request->get('recinto_id');
        $fecha = $request->get('fecha');

        if (!$recintoId || !$fecha) {
            return response()->json(['error' => 'Parámetros inválidos'], 400);
        }

        $recinto = Recinto::find($recintoId);
        if (!$recinto) {
            return response()->json(['error' => 'Recinto no encontrado'], 404);
        }

        // Usa el servicio centralizado
        $resultado = $this->disponibilidadService->calcularDisponibilidad($recinto, $fecha);

        if (isset($resultado['error'])) {
            return response()->json(['error' => $resultado['error']], 400);
        }

        return response()->json($resultado);
    }

    private function generarDiasMes($mes)
    {
        $dias = [];
        $primerDia = $mes->copy()->startOfMonth();
        $ultimoDia = $mes->copy()->endOfMonth();

        $diaSemanaInicio = $primerDia->dayOfWeek;

        for ($i = 0; $i < $diaSemanaInicio; $i++) {
            $dias[] = null;
        }

        $diaActual = $primerDia->copy();
        while ($diaActual->lte($ultimoDia)) {
            $dias[] = $diaActual->copy();
            $diaActual->addDay();
        }

        return $dias;
    }

    /**
     * Envía emails de contacto al usuario y al encargado del recinto
     */
    public function enviarContacto(Request $request)
    {
        try {
            // Validar los datos del formulario
            $validated = $request->validate([
                'recinto_id' => 'required|exists:recintos,id',
                'nombre' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'telefono' => 'nullable|string|max:20',
                'mensaje' => 'required|string|max:1000',
            ], [
                'recinto_id.required' => 'Debe seleccionar un recinto',
                'recinto_id.exists' => 'El recinto seleccionado no es válido',
                'nombre.required' => 'El nombre es obligatorio',
                'email.required' => 'El email es obligatorio',
                'email.email' => 'Debe proporcionar un email válido',
                'mensaje.required' => 'El mensaje es obligatorio',
            ]);

            // Obtener el recinto
            $recinto = Recinto::find($validated['recinto_id']);
            
            // Obtener el encargado del recinto
            $encargado = User::where('role', 'encargado_recinto')
                ->where('recinto_asignado_id', $validated['recinto_id'])
                ->where('activo', 1)
                ->first();

            // Si no hay encargado, usar email genérico
            $emailEncargado = $encargado ? $encargado->email : 'reservas@muniarica.cl';

            // Preparar datos para el email de notificación al encargado
            $datosNotificacion = [
                'nombreRecinto' => $recinto->nombre,
                'nombre' => $validated['nombre'],
                'email' => $validated['email'],
                'telefono' => $validated['telefono'],
                'mensaje' => $validated['mensaje'],
            ];

            // Enviar email de confirmación al usuario
            \Mail::to($validated['email'])->send(new \App\Mail\ContactoConfirmacion(
                $validated['nombre'],
                $recinto->nombre
            ));

            // Enviar email de notificación al encargado
            \Mail::to($emailEncargado)->send(new \App\Mail\ContactoNotificacion($datosNotificacion));

            \Log::info("Formulario de contacto enviado. Usuario: {$validated['email']}, Encargado: {$emailEncargado}, Recinto: {$recinto->nombre}");

            return response()->json([
                'success' => true,
                'message' => 'Tu mensaje ha sido enviado correctamente. Recibirás una respuesta pronto.'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Por favor, verifica los datos ingresados.',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            \Log::error('Error enviando formulario de contacto: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Hubo un error al enviar tu mensaje. Por favor, intenta nuevamente.'
            ], 500);
        }
    }
}
