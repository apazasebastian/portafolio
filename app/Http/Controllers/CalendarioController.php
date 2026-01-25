<?php

namespace App\Http\Controllers;

use App\Models\Recinto;
use App\Models\Reserva;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Controlador del Calendario de Reservas
 * 
 * Maneja la visualizacion del calendario donde los usuarios pueden ver
 * que dias y horarios estan disponibles para reservar un recinto deportivo.
 */
class CalendarioController extends Controller
{
    /**
     * Muestra el calendario de disponibilidad de recintos
     * 
     * Esta pagina permite a los ciudadanos ver todos los recintos activos
     * y las reservas existentes de los proximos 30 dias.
     * Ahora puede filtrar por un recinto especifico si se pasa recinto_id
     */
    public function index(Request $request)
    {
        // Obtiene solo los recintos que estan activos y disponibles para reservar
        $recintos = Recinto::activos()->get();
        
        // Verifica si se esta filtrando por un recinto especifico
        $recintoSeleccionado = null;
        $encargadoInfo = null;
        
        if ($request->has('recinto')) {
            $recintoId = $request->get('recinto');
            $recintoSeleccionado = Recinto::activos()->find($recintoId);
            
            // Si existe el recinto, obtener información del encargado desde la base de datos
            if ($recintoSeleccionado) {
                // Buscar encargado asignado a este recinto
                $encargado = User::where('recinto_asignado_id', $recintoId)
                                 ->where('role', 'encargado_recinto')
                                 ->where('activo', 1)
                                 ->first();
                
                // Si se encuentra un encargado, pasar su información
                if ($encargado) {
                    $encargadoInfo = [
                        'nombre' => $encargado->name,
                        'email' => $encargado->email,
                    ];
                }
            }
        }
        
        // Define el rango de fechas a mostrar (desde hoy hasta 30 dias despues)
        $fechaInicio = Carbon::now()->startOfDay();
        $fechaFin = Carbon::now()->addDays(30)->endOfDay();
        
        // Obtiene todas las reservas aprobadas y vigentes para mostrar en el calendario
        // Las agrupa por recinto y por fecha para facilitar la visualizacion
        $reservas = Reserva::with('recinto')
            ->aprobadas()
            ->whereNull('fecha_cancelacion')
            ->whereBetween('fecha_reserva', [$fechaInicio, $fechaFin])
            ->get()
            ->groupBy(['recinto_id', 'fecha_reserva']);
        
        return view('calendario.index', compact('recintos', 'reservas', 'fechaInicio', 'fechaFin', 'recintoSeleccionado', 'encargadoInfo'));
    }
    
    /**
     * Consulta la disponibilidad horaria de un recinto para una fecha especifica
     * 
     * Esta funcion es llamada por JavaScript cuando el usuario selecciona
     * una fecha en el calendario. Retorna todas las franjas horarias del dia
     * indicando cuales estan libres, ocupadas o bloqueadas.
     */
    public function disponibilidad(Request $request)
    {
        $recintoId = $request->get('recinto_id');
        $fecha = $request->get('fecha');
        
        // Valida que se hayan enviado los parametros requeridos
        if (!$recintoId || !$fecha) {
            return response()->json(['error' => 'Parámetros inválidos'], 400);
        }
        
        // Busca el recinto en la base de datos
        $recinto = Recinto::find($recintoId);
        if (!$recinto) {
            return response()->json(['error' => 'Recinto no encontrado'], 404);
        }
        
        // Convierte la fecha recibida a un formato que podamos trabajar
        try {
            $fechaCarbon = Carbon::parse($fecha);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Fecha inválida'], 400);
        }
        
        // Obtiene el dia de la semana (lunes, martes, etc.) para verificar cierres
        $diaSemana = strtolower($fechaCarbon->format('l'));
        $fechaString = $fechaCarbon->format('Y-m-d');
        
        // Lee la configuracion de dias cerrados del recinto
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
        
        // Verifica si este dia de la semana esta cerrado completamente
        // Por ejemplo: todos los domingos cerrado
        $diasCompletos = [];
        if (isset($diasCerrados['dias_completos']) && is_array($diasCerrados['dias_completos'])) {
            $diasCompletos = $diasCerrados['dias_completos'];
        } elseif (!isset($diasCerrados['dias_completos']) && !isset($diasCerrados['rangos_bloqueados'])) {
            $diasCompletos = $diasCerrados;
        }
        
        $esDiaCerrado = in_array($diaSemana, $diasCompletos);
        
        // Busca bloqueos especificos para esta fecha
        // Por ejemplo: el 25 de diciembre cerrado de 12:00 a 23:00
        $bloqueosFecha = [];
        if (isset($diasCerrados['rangos_bloqueados']) && is_array($diasCerrados['rangos_bloqueados'])) {
            foreach ($diasCerrados['rangos_bloqueados'] as $bloqueo) {
                if (isset($bloqueo['fecha']) && $bloqueo['fecha'] === $fechaString) {
                    $bloqueosFecha[] = $bloqueo;
                }
            }
        }
        
        // Obtiene el horario de funcionamiento del recinto (hora apertura y cierre)
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
        
        // Si no hay horarios definidos, usa valores por defecto (8am a 11pm)
        $horaInicio = $horarios['inicio'] ?? '08:00';
        $horaFin = $horarios['fin'] ?? '23:00';
        
        // Obtiene las reservas existentes para esta fecha (aprobadas o pendientes)
        $reservas = Reserva::where('recinto_id', $recintoId)
            ->where('fecha_reserva', $fecha)
            ->whereIn('estado', ['aprobada', 'pendiente'])
            ->whereNull('fecha_cancelacion')
            ->orderBy('hora_inicio')
            ->get();
        
        // Genera las franjas horarias del dia (una por cada hora)
        // Cada franja indica si esta disponible, ocupada o bloqueada
        $franjasHorarias = [];
        try {
            $timezone = config('app.timezone', 'UTC');
            $horaActual = Carbon::createFromFormat('H:i', $horaInicio, $timezone);
            $horaFinCarbon = Carbon::createFromFormat('H:i', $horaFin, $timezone);
            
            // Limite de seguridad para evitar bucles infinitos
            $maxIteraciones = 24;
            $iteracion = 0;
            
            // Recorre hora por hora desde la apertura hasta el cierre
            while ($horaActual < $horaFinCarbon && $iteracion < $maxIteraciones) {
                $siguienteHora = $horaActual->copy()->addHour();
                
                $ocupada = false;
                $bloqueada = false;
                $reservaInfo = null;
                $motivoBloqueo = null;
                
                // Verifica si esta hora tiene algun bloqueo configurado
                foreach ($bloqueosFecha as $bloqueo) {
                    try {
                        $bloqueInicio = Carbon::createFromFormat('H:i', $bloqueo['hora_inicio'], $timezone);
                        $bloqueFin = Carbon::createFromFormat('H:i', $bloqueo['hora_fin'], $timezone);
                        
                        // Si la franja actual se cruza con el bloqueo, se marca como bloqueada
                        if ($horaActual->lt($bloqueFin) && $siguienteHora->gt($bloqueInicio)) {
                            $bloqueada = true;
                            $motivoBloqueo = $bloqueo['motivo'] ?? 'Bloqueado';
                            break;
                        }
                    } catch (\Exception $e) {
                        continue;
                    }
                }
                
                // Si no esta bloqueada, verifica si hay alguna reserva en esta hora
                if (!$bloqueada) {
                    foreach ($reservas as $reserva) {
                        try {
                            $reservaHoraInicio = $reserva->hora_inicio;
                            $reservaHoraFin = $reserva->hora_fin;
                            
                            // Convierte las horas a un formato comparable
                            if ($reservaHoraInicio instanceof Carbon) {
                                $reservaHoraInicio = $reservaHoraInicio->format('H:i');
                            }
                            if ($reservaHoraFin instanceof Carbon) {
                                $reservaHoraFin = $reservaHoraFin->format('H:i');
                            }
                            
                            $reservaInicio = Carbon::createFromFormat('H:i', $reservaHoraInicio, $timezone);
                            $reservaFin = Carbon::createFromFormat('H:i', $reservaHoraFin, $timezone);
                            
                            // Si la franja actual se cruza con la reserva, se marca como ocupada
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
                            continue;
                        }
                    }
                }
                
                // Agrega la informacion de esta franja horaria al resultado
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
        
        // Retorna toda la informacion de disponibilidad en formato JSON
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
    
    /**
     * Calcula el estado general de un dia para un recinto
     * Retorna: 'DISPONIBLE', 'OCUPADO', o 'MANTENIMIENTO'
     */
    public function estadoDia(Request $request)
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
        
        // Verificar si esta cerrado por mantenimiento
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
        
        $diasCompletos = [];
        if (isset($diasCerrados['dias_completos']) && is_array($diasCerrados['dias_completos'])) {
            $diasCompletos = $diasCerrados['dias_completos'];
        } elseif (!isset($diasCerrados['dias_completos']) && !isset($diasCerrados['rangos_bloqueados'])) {
            $diasCompletos = $diasCerrados;
        }
        
        if (in_array($diaSemana, $diasCompletos)) {
            return response()->json(['estado' => 'MANTENIMIENTO']);
        }
        
        // Obtener horarios y calcular bloques totales
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
        
        try {
            $horaActual = Carbon::createFromFormat('H:i', $horaInicio);
            $horaFinCarbon = Carbon::createFromFormat('H:i', $horaFin);
            
            $totalBloques = 0;
            $bloquesOcupados = 0;
            
            while ($horaActual < $horaFinCarbon) {
                $totalBloques++;
                $siguienteHora = $horaActual->copy()->addHour();
                
                // Verificar si esta ocupado
                $disponible = $recinto->disponibleEn($fecha, $horaActual->format('H:i'), $siguienteHora->format('H:i'));
                
                if (!$disponible) {
                    $bloquesOcupados++;
                }
                
                $horaActual = $siguienteHora;
            }
            
            // Si todos los bloques estan ocupados, retorna OCUPADO
            if ($totalBloques > 0 && $bloquesOcupados === $totalBloques) {
                return response()->json(['estado' => 'OCUPADO']);
            }
            
            // Si hay al menos un bloque disponible, retorna DISPONIBLE
            return response()->json(['estado' => 'DISPONIBLE']);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error calculando estado'], 500);
        }
    }
    
    /**
     * Obtiene los estados de todos los días de un mes
     * Retorna un array con fecha => estado para optimizar carga del calendario
     */
    public function estadosMes(Request $request)
    {
        $recintoId = $request->get('recinto_id');
        $año = $request->get('año');
        $mes = $request->get('mes'); // 1-12
        
        if (!$recintoId || !$año || !$mes) {
            return response()->json(['error' => 'Parámetros inválidos'], 400);
        }
        
        $recinto = Recinto::find($recintoId);
        if (!$recinto) {
            return response()->json(['error' => 'Recinto no encontrado'], 404);
        }
        
        $estados = [];
        
        // Calcular primer y último día del mes
        $primerDia = Carbon::create($año, $mes, 1);
        $ultimoDia = $primerDia->copy()->endOfMonth();
        
        // Calcular límites de reserva
        $hoy = Carbon::now()->startOfDay();
        $fechaMaxima = Carbon::now()->addDays(60)->endOfDay();
        
        // Iterar cada día del mes
        for ($dia = $primerDia->copy(); $dia->lte($ultimoDia); $dia->addDay()) {
            $fechaString = $dia->format('Y-m-d');
            
            // Si es pasado, actual o fuera de rango, no procesar
            if ($dia->lte($hoy) || $dia->gt($fechaMaxima)) {
                continue;
            }
            
            // Calcular estado del día
            $diaSemana = strtolower($dia->format('l'));
            
            // Verificar si está cerrado por mantenimiento
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
            
            $diasCompletos = [];
            if (isset($diasCerrados['dias_completos']) && is_array($diasCerrados['dias_completos'])) {
                $diasCompletos = $diasCerrados['dias_completos'];
            } elseif (!isset($diasCerrados['dias_completos']) && !isset($diasCerrados['rangos_bloqueados'])) {
                $diasCompletos = $diasCerrados;
            }
            
            if (in_array($diaSemana, $diasCompletos)) {
                $estados[$fechaString] = 'MANTENIMIENTO';
                continue;
            }
            
            // Obtener horarios
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
            
            try {
                $horaActual = Carbon::createFromFormat('H:i', $horaInicio);
                $horaFinCarbon = Carbon::createFromFormat('H:i', $horaFin);
                
                $totalBloques = 0;
                $bloquesOcupados = 0;
                
                while ($horaActual < $horaFinCarbon) {
                    $totalBloques++;
                    $siguienteHora = $horaActual->copy()->addHour();
                    
                    // Verificar si está ocupado
                    $disponible = $recinto->disponibleEn($fechaString, $horaActual->format('H:i'), $siguienteHora->format('H:i'));
                    
                    if (!$disponible) {
                        $bloquesOcupados++;
                    }
                    
                    $horaActual = $siguienteHora;
                }
                
                // Determinar estado
                if ($totalBloques > 0 && $bloquesOcupados === $totalBloques) {
                    $estados[$fechaString] = 'OCUPADO';
                } else {
                    $estados[$fechaString] = 'DISPONIBLE';
                }
                
            } catch (\Exception $e) {
                $estados[$fechaString] = 'DISPONIBLE';
            }
        }
        
        return response()->json(['estados' => $estados]);
    }
}
