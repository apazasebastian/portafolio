<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\Recinto;
use App\Models\Reserva;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
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
            'mesActual',
            'mesSiguiente',
            'diasMesActual',
            'diasMesSiguiente'
        ));
    }
    
    // API para obtener disponibilidad de un recinto en una fecha específica
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
        
        $fechaCarbon = Carbon::parse($fecha);
        $diaSemana = strtolower($fechaCarbon->format('l')); // monday, tuesday, etc.
        
        // Verificar si el recinto está cerrado ese día
        $diasCerrados = is_array($recinto->dias_cerrados) 
            ? $recinto->dias_cerrados 
            : ($recinto->dias_cerrados ? json_decode($recinto->dias_cerrados, true) : []);
        
        $esDiaCerrado = in_array($diaSemana, $diasCerrados ?? []);
        
        // Obtener horarios disponibles
        $horarios = is_array($recinto->horarios_disponibles) 
            ? $recinto->horarios_disponibles 
            : json_decode($recinto->horarios_disponibles, true);
        
        $horaInicio = $horarios['inicio'] ?? '08:00';
        $horaFin = $horarios['fin'] ?? '23:00';
        
        // Obtener reservas aprobadas para ese día con información de organización y deporte
        $reservas = Reserva::where('recinto_id', $recintoId)
            ->where('fecha_reserva', $fecha)
            ->where('estado', 'aprobada')
            ->orderBy('hora_inicio')
            ->get();
        
        // Generar franjas horarias cada hora
        $franjasHorarias = [];
        $horaActual = Carbon::parse($horaInicio);
        $horaFinCarbon = Carbon::parse($horaFin);
        
        while ($horaActual < $horaFinCarbon) {
            $siguienteHora = $horaActual->copy()->addHour();
            
            $ocupada = false;
            $reservaInfo = null;
            
            // Verificar si esta franja está ocupada
            foreach ($reservas as $reserva) {
                $reservaInicio = Carbon::parse($reserva->hora_inicio);
                $reservaFin = Carbon::parse($reserva->hora_fin);
                
                if ($horaActual->lt($reservaFin) && $siguienteHora->gt($reservaInicio)) {
                    $ocupada = true;
                    $reservaInfo = [
                        'nombre_organizacion' => $reserva->nombre_organizacion,
                        'deporte' => $reserva->deporte ?? 'No especificado',
                        'hora_inicio' => $reserva->hora_inicio,
                        'hora_fin' => $reserva->hora_fin
                    ];
                    break;
                }
            }
            
            $franjasHorarias[] = [
                'hora_inicio' => $horaActual->format('H:i'),
                'hora_fin' => $siguienteHora->format('H:i'),
                'disponible' => !$ocupada && !$esDiaCerrado,
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
            'franjas_horarias' => $franjasHorarias,
            'total_reservas' => $reservas->count()
        ]);
    }
    
    private function generarDiasMes($mes)
    {
        $dias = [];
        $primerDia = $mes->copy()->startOfMonth();
        $ultimoDia = $mes->copy()->endOfMonth();
        
        // Obtener el día de la semana del primer día (0=Domingo, 6=Sábado)
        $diaSemanaInicio = $primerDia->dayOfWeek;
        
        // Agregar días vacíos al inicio
        for ($i = 0; $i < $diaSemanaInicio; $i++) {
            $dias[] = null;
        }
        
        // Agregar todos los días del mes
        $diaActual = $primerDia->copy();
        while ($diaActual->lte($ultimoDia)) {
            $dias[] = $diaActual->copy();
            $diaActual->addDay();
        }
        
        return $dias;
    }
}
