<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * Modelo de Recinto Deportivo
 * 
 * Representa un recinto o cancha deportiva que puede ser reservada.
 * Contiene informacion sobre el nombre del recinto, su capacidad maxima,
 * los horarios en que esta disponible, y los dias que permanece cerrado.
 */
class Recinto extends Model
{
    use HasFactory;

    /**
     * Campos que se pueden llenar al crear o actualizar un recinto
     */
    protected $fillable = [
        'nombre', 
        'descripcion', 
        'capacidad_maxima',
        'activo', 
        'horarios_disponibles', 
        'dias_cerrados',
        'imagen_url'
    ];

    /**
     * Conversion automatica de tipos de datos
     */
    protected $casts = [
        'horarios_disponibles' => 'array',
        'dias_cerrados' => 'array',
        'activo' => 'boolean'
    ];

    // =========================================================================
    // RELACIONES - Conexiones con otras tablas de la base de datos
    // =========================================================================

    /**
     * Obtiene todas las reservas que se han hecho para este recinto
     */
    public function reservas(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Reserva::class);
    }

    /**
     * Obtiene los encargados asignados a este recinto
     * Los encargados son funcionarios que administran el recinto dia a dia
     */
    public function encargados(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(User::class, 'recinto_asignado_id');
    }

    // =========================================================================
    // VERIFICACION DE DISPONIBILIDAD
    // =========================================================================

    /**
     * Verifica si el recinto esta disponible para una fecha y horario especifico
     * 
     * Esta es la funcion principal para saber si alguien puede reservar.
     * Verifica que:
     * 1. No sea un dia de la semana en que el recinto esta cerrado
     * 2. No tenga un bloqueo especial para esa fecha
     * 3. El horario este dentro del horario de funcionamiento
     * 4. No exista otra reserva en el mismo horario
     */
    public function disponibleEn(string $fecha, string $horaInicio, string $horaFin): bool
    {
        $fechaCarbon = Carbon::parse($fecha);
        $diaSemana = strtolower($fechaCarbon->format('l'));
        $fechaString = $fechaCarbon->format('Y-m-d');

        // Lee la configuracion de dias cerrados del recinto
        $diasCerrados = $this->dias_cerrados;
        if (is_string($diasCerrados)) {
            $diasCerrados = json_decode($diasCerrados, true) ?? [];
        } elseif (!is_array($diasCerrados)) {
            $diasCerrados = [];
        }

        // Primero verifica si es un dia de la semana que siempre esta cerrado
        // Por ejemplo: todos los domingos cerrado
        $diasCompletos = [];
        if (isset($diasCerrados['dias_completos']) && is_array($diasCerrados['dias_completos'])) {
            $diasCompletos = $diasCerrados['dias_completos'];
        } elseif (!isset($diasCerrados['dias_completos']) && !isset($diasCerrados['rangos_bloqueados'])) {
            $diasCompletos = $diasCerrados;
        }

        if (in_array($diaSemana, $diasCompletos)) {
            return false;
        }

        // Verifica si hay bloqueos especiales para esta fecha especifica
        // Por ejemplo: el 25 de diciembre cerrado de 12:00 a 23:00
        if (isset($diasCerrados['rangos_bloqueados']) && is_array($diasCerrados['rangos_bloqueados'])) {
            foreach ($diasCerrados['rangos_bloqueados'] as $bloqueo) {
                if (isset($bloqueo['fecha']) && $bloqueo['fecha'] === $fechaString) {
                    $bloqueInicio = $bloqueo['hora_inicio'] ?? '00:00';
                    $bloqueFin = $bloqueo['hora_fin'] ?? '23:59';
                    
                    // Verifica si el horario solicitado choca con el bloqueo
                    if ($this->hayConflictoHorario($horaInicio, $horaFin, $bloqueInicio, $bloqueFin)) {
                        return false;
                    }
                }
            }
        }

        // Verifica que el horario solicitado este dentro del horario de funcionamiento
        $horarioInicio = $this->horarios_disponibles['inicio'] ?? '08:00';
        $horarioFin = $this->horarios_disponibles['fin'] ?? '23:00';
        
        if ($horaInicio < $horarioInicio || $horaFin > $horarioFin) {
            return false;
        }

        // Finalmente, verifica si hay otras reservas que ocupen el mismo horario
        $conflictos = $this->reservas()
            ->where('fecha_reserva', $fecha)
            ->whereIn('estado', ['aprobada', 'pendiente'])
            ->whereNull('fecha_cancelacion')
            ->where(function($query) use ($horaInicio, $horaFin) {
                // Busca cualquier reserva que se cruce con el horario solicitado
                $query->where(function($q) use ($horaInicio, $horaFin) {
                    $q->where('hora_inicio', '<=', $horaInicio)
                      ->where('hora_fin', '>', $horaInicio);
                })->orWhere(function($q) use ($horaInicio, $horaFin) {
                    $q->where('hora_inicio', '<', $horaFin)
                      ->where('hora_fin', '>=', $horaFin);
                })->orWhere(function($q) use ($horaInicio, $horaFin) {
                    $q->where('hora_inicio', '>=', $horaInicio)
                      ->where('hora_fin', '<=', $horaFin);
                });
            })
            ->exists();

        // Si no hay conflictos, esta disponible
        return !$conflictos;
    }

    /**
     * Verifica si dos rangos de horarios se cruzan o solapan
     * 
     * Por ejemplo, 10:00-12:00 y 11:00-13:00 se cruzan
     * pero 10:00-12:00 y 14:00-16:00 no se cruzan
     */
    private function hayConflictoHorario(string $inicio1, string $fin1, string $inicio2, string $fin2): bool
    {
        $t1_inicio = strtotime($inicio1);
        $t1_fin = strtotime($fin1);
        $t2_inicio = strtotime($inicio2);
        $t2_fin = strtotime($fin2);

        return ($t1_inicio < $t2_fin && $t1_fin > $t2_inicio);
    }

    /**
     * Obtiene todas las reservas confirmadas para un dia especifico
     * Util para mostrar la agenda del dia en el panel del encargado
     */
    public function reservasDelDia(string $fecha): \Illuminate\Database\Eloquent\Collection
    {
        return $this->reservas()
            ->where('fecha_reserva', $fecha)
            ->where('estado', 'aprobada')
            ->whereNull('fecha_cancelacion')
            ->orderBy('hora_inicio')
            ->get();
    }

    // =========================================================================
    // SCOPES - Filtros reutilizables para consultas
    // =========================================================================

    /**
     * Filtra solo los recintos que estan activos y disponibles para reservar
     * Los recintos inactivos no aparecen en el calendario publico
     */
    public function scopeActivos(\Illuminate\Database\Eloquent\Builder $query): void
    {
        $query->where('activo', true);
    }

    // =========================================================================
    // METODOS DE CONSULTA
    // =========================================================================

    /**
     * Obtiene los bloqueos especiales configurados para una fecha especifica
     * Se usa para mostrar al usuario por que no puede reservar ciertos horarios
     */
    public function getBloqueosPorFecha(string $fecha): array
    {
        $fechaString = Carbon::parse($fecha)->format('Y-m-d');
        $diasCerrados = $this->dias_cerrados;
        
        if (!is_array($diasCerrados) || !isset($diasCerrados['rangos_bloqueados'])) {
            return [];
        }

        $bloqueos = [];
        foreach ($diasCerrados['rangos_bloqueados'] as $bloqueo) {
            if (isset($bloqueo['fecha']) && $bloqueo['fecha'] === $fechaString) {
                $bloqueos[] = $bloqueo;
            }
        }

        return $bloqueos;
    }
}