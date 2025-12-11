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
     * ⚠️ ACTUALIZADO: API de disponibilidad con fechas específicas ⚠️
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
        
        $fechaCarbon = Carbon::parse($fecha);
        $diaSemana = strtolower($fechaCarbon->format('l'));
        $fechaString = $fechaCarbon->format('Y-m-d');
        
        // Obtener días cerrados
        $diasCerrados = is_array($recinto->dias_cerrados) 
            ? $recinto->dias_cerrados 
            : ($recinto->dias_cerrados ? json_decode($recinto->dias_cerrados, true) : []);
        
        // Verificar si es día completo cerrado
        $diasCompletos = [];
        if (isset($diasCerrados['dias_completos']) && is_array($diasCerrados['dias_completos'])) {
            $diasCompletos = $diasCerrados['dias_completos'];
        } elseif (!isset($diasCerrados['dias_completos']) && !isset($diasCerrados['rangos_bloqueados'])) {
            $diasCompletos = $diasCerrados;
        }
        
        $esDiaCerrado = in_array($diaSemana, $diasCompletos);
        
        // ⚠️ OBTENER BLOQUEOS PARA ESTA FECHA ESPECÍFICA ⚠️
        $bloqueosFecha = [];
        if (isset($diasCerrados['rangos_bloqueados']) && is_array($diasCerrados['rangos_bloqueados'])) {
            foreach ($diasCerrados['rangos_bloqueados'] as $bloqueo) {
                if (isset($bloqueo['fecha']) && $bloqueo['fecha'] === $fechaString) {
                    $bloqueosFecha[] = $bloqueo;
                }
            }
        }
        
        // Obtener horarios disponibles
        $horarios = is_array($recinto->horarios_disponibles) 
            ? $recinto->horarios_disponibles 
            : json_decode($recinto->horarios_disponibles, true);
        
        $horaInicio = $horarios['inicio'] ?? '08:00';
        $horaFin = $horarios['fin'] ?? '23:00';
        
        // Obtener reservas
        $reservas = Reserva::where('recinto_id', $recintoId)
            ->where('fecha_reserva', $fecha)
            ->whereIn('estado', ['aprobada', 'pendiente'])
            ->whereNull('fecha_cancelacion')
            ->orderBy('hora_inicio')
            ->get();
        
        // Generar franjas horarias
        $franjasHorarias = [];
        $horaActual = Carbon::parse($horaInicio);
        $horaFinCarbon = Carbon::parse($horaFin);
        
        while ($horaActual < $horaFinCarbon) {
            $siguienteHora = $horaActual->copy()->addHour();
            
            $ocupada = false;
            $bloqueada = false;
            $reservaInfo = null;
            $motivoBloqueo = null;
            
            // Verificar bloqueos para esta fecha específica
            foreach ($bloqueosFecha as $bloqueo) {
                $bloqueInicio = Carbon::parse($bloqueo['hora_inicio']);
                $bloqueFin = Carbon::parse($bloqueo['hora_fin']);
                
                if ($horaActual->lt($bloqueFin) && $siguienteHora->gt($bloqueInicio)) {
                    $bloqueada = true;
                    $motivoBloqueo = $bloqueo['motivo'] ?? 'Bloqueado';
                    break;
                }
            }
            
            // Verificar reservas
            if (!$bloqueada) {
                foreach ($reservas as $reserva) {
                    $reservaInicio = Carbon::parse($reserva->hora_inicio);
                    $reservaFin = Carbon::parse($reserva->hora_fin);
                    
                    if ($horaActual->lt($reservaFin) && $siguienteHora->gt($reservaInicio)) {
                        $ocupada = true;
                        $reservaInfo = [
                            'nombre_organizacion' => $reserva->nombre_organizacion,
                            'deporte' => $reserva->deporte ?? 'No especificado',
                            'hora_inicio' => $reserva->hora_inicio,
                            'hora_fin' => $reserva->hora_fin,
                            'estado' => $reserva->estado,
                        ];
                        break;
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