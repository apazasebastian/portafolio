<?php

namespace App\Http\Controllers;

use App\Models\Recinto;
use App\Models\Reserva;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CalendarioController extends Controller
{
    public function index()
    {
        $recintos = Recinto::activos()->get();
        
        $fechaInicio = Carbon::now()->startOfDay();
        $fechaFin = Carbon::now()->addDays(30)->endOfDay();
        
        $reservas = Reserva::with('recinto')
            ->aprobadas()
            ->whereNull('fecha_cancelacion')
            ->whereBetween('fecha_reserva', [$fechaInicio, $fechaFin])
            ->get()
            ->groupBy(['recinto_id', 'fecha_reserva']);
        
        return view('calendario.index', compact('recintos', 'reservas', 'fechaInicio', 'fechaFin'));
    }
    
    /**
     *  ACTUALIZADO: API de disponibilidad con fechas específicas 
     */
    public function disponibilidad(Request $request)
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
        
        try {
            $fechaCarbon = Carbon::parse($fecha);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Fecha inválida'], 400);
        }
        
        $diaSemana = strtolower($fechaCarbon->format('l'));
        $fechaString = $fechaCarbon->format('Y-m-d');
        
        // Obtener días cerrados con manejo de errores
        $diasCerrados = [];
        if (is_array($recinto->dias_cerrados)) {
            $diasCerrados = $recinto->dias_cerrados;
        } elseif (is_string($recinto->dias_cerrados) && !empty($recinto->dias_cerrados)) {
            try {
                $diasCerrados = json_decode($recinto->dias_cerrados, true) ?? [];
            } catch (\Exception $e) {
                $diasCerrados = [];
            }
        }
        
        // Verificar si es día completo cerrado
        $diasCompletos = [];
        if (isset($diasCerrados['dias_completos']) && is_array($diasCerrados['dias_completos'])) {
            $diasCompletos = $diasCerrados['dias_completos'];
        } elseif (!isset($diasCerrados['dias_completos']) && !isset($diasCerrados['rangos_bloqueados'])) {
            $diasCompletos = $diasCerrados;
        }
        
        $esDiaCerrado = in_array($diaSemana, $diasCompletos);
        
        //  OBTENER BLOQUEOS PARA ESTA FECHA ESPECÍFICA 
        $bloqueosFecha = [];
        if (isset($diasCerrados['rangos_bloqueados']) && is_array($diasCerrados['rangos_bloqueados'])) {
            foreach ($diasCerrados['rangos_bloqueados'] as $bloqueo) {
                if (isset($bloqueo['fecha']) && $bloqueo['fecha'] === $fechaString) {
                    $bloqueosFecha[] = $bloqueo;
                }
            }
        }
        
        // Obtener horarios disponibles con manejo de errores
        $horarios = [];
        if (is_array($recinto->horarios_disponibles)) {
            $horarios = $recinto->horarios_disponibles;
        } elseif (is_string($recinto->horarios_disponibles) && !empty($recinto->horarios_disponibles)) {
            try {
                $horarios = json_decode($recinto->horarios_disponibles, true) ?? [];
            } catch (\Exception $e) {
                $horarios = [];
            }
        }
        
        $horaInicio = $horarios['inicio'] ?? '08:00';
        $horaFin = $horarios['fin'] ?? '23:00';
        
        // Obtener reservas
        $reservas = Reserva::where('recinto_id', $recintoId)
            ->where('fecha_reserva', $fecha)
            ->whereIn('estado', ['aprobada', 'pendiente'])
            ->whereNull('fecha_cancelacion')
            ->orderBy('hora_inicio')
            ->get();
        
        // Generar franjas horarias con manejo de errores
        $franjasHorarias = [];
        try {
            // Usar timezone de la aplicación en lugar de hardcodear
            $timezone = config('app.timezone', 'UTC');
            $horaActual = Carbon::createFromFormat('H:i', $horaInicio, $timezone);
            $horaFinCarbon = Carbon::createFromFormat('H:i', $horaFin, $timezone);
            
            // Protección contra bucles infinitos
            $maxIteraciones = 24;
            $iteracion = 0;
            
            while ($horaActual < $horaFinCarbon && $iteracion < $maxIteraciones) {
                $siguienteHora = $horaActual->copy()->addHour();
                
                $ocupada = false;
                $bloqueada = false;
                $reservaInfo = null;
                $motivoBloqueo = null;
                
                // Verificar bloqueos para esta fecha específica
                foreach ($bloqueosFecha as $bloqueo) {
                    try {
                        $bloqueInicio = Carbon::createFromFormat('H:i', $bloqueo['hora_inicio'], $timezone);
                        $bloqueFin = Carbon::createFromFormat('H:i', $bloqueo['hora_fin'], $timezone);
                        
                        if ($horaActual->lt($bloqueFin) && $siguienteHora->gt($bloqueInicio)) {
                            $bloqueada = true;
                            $motivoBloqueo = $bloqueo['motivo'] ?? 'Bloqueado';
                            break;
                        }
                    } catch (\Exception $e) {
                        // Si hay error parseando un bloqueo, continuar con el siguiente
                        continue;
                    }
                }
                
                // Verificar reservas
                if (!$bloqueada) {
                    foreach ($reservas as $reserva) {
                        try {
                            // Parsear las horas de la reserva
                            $reservaHoraInicio = $reserva->hora_inicio;
                            $reservaHoraFin = $reserva->hora_fin;
                            
                            // Si son objetos Carbon, convertirlos a string
                            if ($reservaHoraInicio instanceof Carbon) {
                                $reservaHoraInicio = $reservaHoraInicio->format('H:i');
                            }
                            if ($reservaHoraFin instanceof Carbon) {
                                $reservaHoraFin = $reservaHoraFin->format('H:i');
                            }
                            
                            $reservaInicio = Carbon::createFromFormat('H:i', $reservaHoraInicio, $timezone);
                            $reservaFin = Carbon::createFromFormat('H:i', $reservaHoraFin, $timezone);
                            
                            if ($horaActual->lt($reservaFin) && $siguienteHora->gt($reservaInicio)) {
                                $ocupada = true;
                                $reservaInfo = [
                                    'nombre_organizacion' => $reserva->nombre_organizacion ?? 'Sin nombre',
                                    'deporte' => $reserva->deporte ?? 'No especificado',
                                    'hora_inicio' => $reservaHoraInicio,
                                    'hora_fin' => $reservaHoraFin,
                                    'estado' => $reserva->estado ?? 'pendiente',
                                ];
                                break;
                            }
                        } catch (\Exception $e) {
                            // Si hay error parseando una reserva, continuar con la siguiente
                            continue;
                        }
                    }
                }
                
                $franjasHorarias[] = [
                    'hora_inicio' => $horaActual->format('H:i'),
                    'hora_fin' => $siguienteHora->format('H:i'),
                    'disponible' => !$ocupada && !$esDiaCerrado && !$bloqueada,
                    'bloqueada' => $bloqueada,
                    'motivo_bloqueo' => $motivoBloqueo,
                    'reserva' => $reservaInfo
                ];
                
                $horaActual = $siguienteHora;
                $iteracion++;
            }
        } catch (\Exception $e) {
            \Log::error('Error generando franjas horarias: ' . $e->getMessage());
            return response()->json(['error' => 'Error generando disponibilidad'], 500);
        }
        
        return response()->json([
            'recinto' => $recinto->nombre,
            'fecha' => $fechaCarbon->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY'),
            'cerrado' => $esDiaCerrado,
            'motivo_cierre' => $esDiaCerrado ? 'Día de mantenimiento' : null,
            'horario_general' => [
                'inicio' => $horaInicio,
                'fin' => $horaFin
            ],
            'horarios' => $franjasHorarias,
            'total_reservas' => $reservas->count(),
            'bloqueos_dia' => $bloqueosFecha
        ]);
    }
}
