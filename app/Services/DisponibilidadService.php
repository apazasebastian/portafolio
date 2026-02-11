<?php

namespace App\Services;

use App\Models\Recinto;
use App\Models\Reserva;
use Carbon\Carbon;

/**
 * Servicio de Disponibilidad
 *
 * Centraliza toda la lógica de cálculo de disponibilidad de recintos.
 * Antes esta lógica estaba duplicada en CalendarioController y HomeController.
 */
class DisponibilidadService
{
    /**
     * Parsea los días cerrados de un recinto
     * Maneja tanto arrays como strings JSON
     */
    public function parsearDiasCerrados($recinto): array
    {
        if (is_array($recinto->dias_cerrados)) {
            return $recinto->dias_cerrados;
        }

        if (is_string($recinto->dias_cerrados) && !empty($recinto->dias_cerrados)) {
            try {
                return json_decode($recinto->dias_cerrados, true) ?? [];
            } catch (\Exception $e) {
                return [];
            }
        }

        return [];
    }

    /**
     * Parsea los horarios disponibles de un recinto
     * Retorna hora de inicio y fin con valores por defecto
     */
    public function parsearHorarios($recinto): array
    {
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

        return [
            'inicio' => $horarios['inicio'] ?? '08:00',
            'fin' => $horarios['fin'] ?? '23:00'
        ];
    }

    /**
     * Obtiene los días completos cerrados (ej: todos los domingos)
     */
    public function obtenerDiasCompletos(array $diasCerrados): array
    {
        if (isset($diasCerrados['dias_completos']) && is_array($diasCerrados['dias_completos'])) {
            return $diasCerrados['dias_completos'];
        }

        // Si no tiene estructura nueva, asume que todo el array son días
        if (!isset($diasCerrados['dias_completos']) && !isset($diasCerrados['rangos_bloqueados'])) {
            return $diasCerrados;
        }

        return [];
    }

    /**
     * Obtiene los bloqueos específicos para una fecha
     */
    public function obtenerBloqueosFecha(array $diasCerrados, string $fechaString): array
    {
        $bloqueos = [];

        if (isset($diasCerrados['rangos_bloqueados']) && is_array($diasCerrados['rangos_bloqueados'])) {
            foreach ($diasCerrados['rangos_bloqueados'] as $bloqueo) {
                if (isset($bloqueo['fecha']) && $bloqueo['fecha'] === $fechaString) {
                    $bloqueos[] = $bloqueo;
                }
            }
        }

        return $bloqueos;
    }

    /**
     * Verifica si un día de la semana está cerrado
     */
    public function esDiaCerrado(array $diasCompletos, string $diaSemana): bool
    {
        return in_array($diaSemana, $diasCompletos);
    }

    /**
     * Obtiene las reservas de un recinto para una fecha
     */
    public function obtenerReservas(int $recintoId, string $fecha)
    {
        return Reserva::where('recinto_id', $recintoId)
            ->where('fecha_reserva', $fecha)
            ->whereIn('estado', ['aprobada', 'pendiente'])
            ->whereNull('fecha_cancelacion')
            ->orderBy('hora_inicio')
            ->get();
    }

    /**
     * Genera las franjas horarias del día con su estado
     */
    public function generarFranjasHorarias(
        string $horaInicio,
        string $horaFin,
        $reservas,
        array $bloqueosFecha,
        bool $esDiaCerrado
    ): array {
        $franjasHorarias = [];
        $timezone = config('app.timezone', 'UTC');

        try {
            $horaActual = Carbon::createFromFormat('H:i', $horaInicio, $timezone);
            $horaFinCarbon = Carbon::createFromFormat('H:i', $horaFin, $timezone);

            $maxIteraciones = 24;
            $iteracion = 0;

            while ($horaActual < $horaFinCarbon && $iteracion < $maxIteraciones) {
                $siguienteHora = $horaActual->copy()->addHour();

                $ocupada = false;
                $bloqueada = false;
                $reservaInfo = null;
                $motivoBloqueo = null;

                // Verificar bloqueos
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
                        continue;
                    }
                }

                // Verificar reservas si no está bloqueada
                if (!$bloqueada) {
                    foreach ($reservas as $reserva) {
                        try {
                            $reservaHoraInicio = $reserva->hora_inicio;
                            $reservaHoraFin = $reserva->hora_fin;

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
            return [];
        }

        return $franjasHorarias;
    }

    /**
     * Calcula la disponibilidad completa de un recinto para una fecha
     * Este es el método principal que reemplaza la lógica duplicada
     */
    public function calcularDisponibilidad(Recinto $recinto, string $fecha): array
    {
        try {
            $fechaCarbon = Carbon::parse($fecha);
        } catch (\Exception $e) {
            return ['error' => 'Fecha inválida'];
        }

        $diaSemana = strtolower($fechaCarbon->format('l'));
        $fechaString = $fechaCarbon->format('Y-m-d');

        // Parsear configuración del recinto
        $diasCerrados = $this->parsearDiasCerrados($recinto);
        $diasCompletos = $this->obtenerDiasCompletos($diasCerrados);
        $bloqueosFecha = $this->obtenerBloqueosFecha($diasCerrados, $fechaString);
        $horarios = $this->parsearHorarios($recinto);

        $esDiaCerrado = $this->esDiaCerrado($diasCompletos, $diaSemana);

        // Obtener reservas
        $reservas = $this->obtenerReservas($recinto->id, $fecha);

        // Generar franjas horarias
        $franjasHorarias = $this->generarFranjasHorarias(
            $horarios['inicio'],
            $horarios['fin'],
            $reservas,
            $bloqueosFecha,
            $esDiaCerrado
        );

        return [
            'recinto' => $recinto->nombre,
            'fecha' => $fechaCarbon->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY'),
            'cerrado' => $esDiaCerrado,
            'motivo_cierre' => $esDiaCerrado ? 'Día de mantenimiento' : null,
            'horario_general' => $horarios,
            'horarios' => $franjasHorarias,
            'total_reservas' => $reservas->count(),
            'bloqueos_dia' => $bloqueosFecha
        ];
    }

    /**
     * Calcula el estado de un día: DISPONIBLE, OCUPADO o MANTENIMIENTO
     */
    public function calcularEstadoDia(Recinto $recinto, string $fecha): string
    {
        try {
            $fechaCarbon = Carbon::parse($fecha);
        } catch (\Exception $e) {
            return 'DISPONIBLE';
        }

        $diaSemana = strtolower($fechaCarbon->format('l'));
        $fechaString = $fechaCarbon->format('Y-m-d');

        // Verificar si es día de mantenimiento
        $diasCerrados = $this->parsearDiasCerrados($recinto);
        $diasCompletos = $this->obtenerDiasCompletos($diasCerrados);

        if ($this->esDiaCerrado($diasCompletos, $diaSemana)) {
            return 'MANTENIMIENTO';
        }

        // Calcular bloques ocupados
        $horarios = $this->parsearHorarios($recinto);

        try {
            $horaActual = Carbon::createFromFormat('H:i', $horarios['inicio']);
            $horaFinCarbon = Carbon::createFromFormat('H:i', $horarios['fin']);

            $totalBloques = 0;
            $bloquesOcupados = 0;

            while ($horaActual < $horaFinCarbon) {
                $totalBloques++;
                $siguienteHora = $horaActual->copy()->addHour();

                $disponible = $recinto->disponibleEn(
                    $fechaString,
                    $horaActual->format('H:i'),
                    $siguienteHora->format('H:i')
                );

                if (!$disponible) {
                    $bloquesOcupados++;
                }

                $horaActual = $siguienteHora;
            }

            if ($totalBloques > 0 && $bloquesOcupados === $totalBloques) {
                return 'OCUPADO';
            }

        } catch (\Exception $e) {
            // En caso de error, asumir disponible
        }

        return 'DISPONIBLE';
    }

    /**
     * Calcula los estados de todos los días de un mes
     * OPTIMIZADO: Pre-carga todas las reservas del mes en una sola consulta
     */
    public function calcularEstadosMes(Recinto $recinto, int $año, int $mes): array
    {
        $estados = [];

        $primerDia = Carbon::create($año, $mes, 1);
        $ultimoDia = $primerDia->copy()->endOfMonth();

        $hoy = Carbon::now()->startOfDay();
        $fechaMaxima = Carbon::now()->addDays(60)->endOfDay();

        // OPTIMIZACIÓN: Pre-cargar todas las reservas del mes de una sola vez
        $reservasPorFecha = Reserva::where('recinto_id', $recinto->id)
            ->whereYear('fecha_reserva', $año)
            ->whereMonth('fecha_reserva', $mes)
            ->whereIn('estado', ['aprobada', 'pendiente'])
            ->whereNull('fecha_cancelacion')
            ->get()
            ->groupBy('fecha_reserva');

        // Pre-parsear configuración del recinto (solo una vez)
        $diasCerrados = $this->parsearDiasCerrados($recinto);
        $diasCompletos = $this->obtenerDiasCompletos($diasCerrados);
        $horarios = $this->parsearHorarios($recinto);

        for ($dia = $primerDia->copy(); $dia->lte($ultimoDia); $dia->addDay()) {
            $fechaString = $dia->format('Y-m-d');

            // Saltar días pasados o fuera de rango
            if ($dia->lte($hoy) || $dia->gt($fechaMaxima)) {
                continue;
            }

            // Calcular estado usando datos pre-cargados
            $estados[$fechaString] = $this->calcularEstadoDiaOptimizado(
                $recinto,
                $dia,
                $diasCompletos,
                $horarios,
                $reservasPorFecha->get($fechaString, collect())
            );
        }

        return $estados;
    }

    /**
     * Versión optimizada de calcularEstadoDia que usa datos pre-cargados
     * Evita consultas repetidas a la base de datos
     */
    private function calcularEstadoDiaOptimizado(
        Recinto $recinto,
        Carbon $fecha,
        array $diasCompletos,
        array $horarios,
        $reservas
    ): string {
        $diaSemana = strtolower($fecha->format('l'));
        $fechaString = $fecha->format('Y-m-d');

        // Verificar si es día de mantenimiento
        if ($this->esDiaCerrado($diasCompletos, $diaSemana)) {
            return 'MANTENIMIENTO';
        }

        // Obtener bloqueos específicos para esta fecha
        $diasCerrados = $this->parsearDiasCerrados($recinto);
        $bloqueosFecha = $this->obtenerBloqueosFecha($diasCerrados, $fechaString);

        // Calcular bloques ocupados o bloqueados
        try {
            $horaActual = Carbon::createFromFormat('H:i', $horarios['inicio']);
            $horaFinCarbon = Carbon::createFromFormat('H:i', $horarios['fin']);
            $timezone = config('app.timezone', 'UTC');

            $totalBloques = 0;
            $bloquesOcupados = 0;

            while ($horaActual < $horaFinCarbon) {
                $totalBloques++;
                $siguienteHora = $horaActual->copy()->addHour();

                $bloqueOcupado = false;

                // 1. Verificar si hay bloqueo específico en este horario
                foreach ($bloqueosFecha as $bloqueo) {
                    try {
                        $bloqueInicio = Carbon::createFromFormat('H:i', $bloqueo['hora_inicio'], $timezone);
                        $bloqueFin = Carbon::createFromFormat('H:i', $bloqueo['hora_fin'], $timezone);

                        // Si el bloque se solapa con el bloqueo, está ocupado
                        if ($horaActual->lt($bloqueFin) && $siguienteHora->gt($bloqueInicio)) {
                            $bloqueOcupado = true;
                            break;
                        }
                    } catch (\Exception $e) {
                        continue;
                    }
                }

                // 2. Si no está bloqueado, verificar si hay reserva
                if (!$bloqueOcupado) {
                    foreach ($reservas as $reserva) {
                        try {
                            $reservaHoraInicio = $reserva->hora_inicio;
                            $reservaHoraFin = $reserva->hora_fin;

                            if ($reservaHoraInicio instanceof Carbon) {
                                $reservaHoraInicio = $reservaHoraInicio->format('H:i');
                            }
                            if ($reservaHoraFin instanceof Carbon) {
                                $reservaHoraFin = $reservaHoraFin->format('H:i');
                            }

                            $reservaInicio = Carbon::createFromFormat('H:i', $reservaHoraInicio, $timezone);
                            $reservaFin = Carbon::createFromFormat('H:i', $reservaHoraFin, $timezone);

                            // Detectar solapamiento
                            if ($horaActual->lt($reservaFin) && $siguienteHora->gt($reservaInicio)) {
                                $bloqueOcupado = true;
                                break;
                            }
                        } catch (\Exception $e) {
                            continue;
                        }
                    }
                }

                if ($bloqueOcupado) {
                    $bloquesOcupados++;
                }

                $horaActual = $siguienteHora;
            }

            // Si TODOS los bloques están ocupados o bloqueados, el día está OCUPADO
            if ($totalBloques > 0 && $bloquesOcupados === $totalBloques) {
                return 'OCUPADO';
            }

        } catch (\Exception $e) {
            // En caso de error, asumir disponible
        }

        return 'DISPONIBLE';
    }
}
