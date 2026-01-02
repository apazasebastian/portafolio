<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Recinto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre', 
        'descripcion', 
        'capacidad_maxima',
        'activo', 
        'horarios_disponibles', 
        'dias_cerrados',
        'imagen_url'
    ];

    protected $casts = [
        'horarios_disponibles' => 'array',
        'dias_cerrados' => 'array',
        'activo' => 'boolean'
    ];

    public function reservas()
    {
        return $this->hasMany(Reserva::class);
    }

    public function encargados()
    {
        return $this->hasMany(User::class, 'recinto_asignado_id');
    }

    /**
     * ⚠️ ACTUALIZADO: Verifica disponibilidad con fechas específicas ⚠️
     */
    public function disponibleEn($fecha, $horaInicio, $horaFin)
    {
        $fechaCarbon = Carbon::parse($fecha);
        $diaSemana = strtolower($fechaCarbon->format('l'));
        $fechaString = $fechaCarbon->format('Y-m-d');

        // Obtener días cerrados
        $diasCerrados = $this->dias_cerrados;
        if (is_string($diasCerrados)) {
            $diasCerrados = json_decode($diasCerrados, true) ?? [];
        } elseif (!is_array($diasCerrados)) {
            $diasCerrados = [];
        }

        // ⚠️ VERIFICAR DÍAS COMPLETOS CERRADOS ⚠️
        $diasCompletos = [];
        if (isset($diasCerrados['dias_completos']) && is_array($diasCerrados['dias_completos'])) {
            $diasCompletos = $diasCerrados['dias_completos'];
        } elseif (!isset($diasCerrados['dias_completos']) && !isset($diasCerrados['rangos_bloqueados'])) {
            // Retrocompatibilidad: si no tiene la nueva estructura, usar el array completo
            $diasCompletos = $diasCerrados;
        }

        if (in_array($diaSemana, $diasCompletos)) {
            return false;
        }

        // ⚠️ VERIFICAR BLOQUEOS POR FECHA ESPECÍFICA ⚠️
        if (isset($diasCerrados['rangos_bloqueados']) && is_array($diasCerrados['rangos_bloqueados'])) {
            foreach ($diasCerrados['rangos_bloqueados'] as $bloqueo) {
                // Verificar si el bloqueo es para esta fecha específica
                if (isset($bloqueo['fecha']) && $bloqueo['fecha'] === $fechaString) {
                    $bloqueInicio = $bloqueo['hora_inicio'] ?? '00:00';
                    $bloqueFin = $bloqueo['hora_fin'] ?? '23:59';
                    
                    // Verificar si hay solapamiento entre el horario solicitado y el bloqueado
                    if ($this->hayConflictoHorario($horaInicio, $horaFin, $bloqueInicio, $bloqueFin)) {
                        return false;
                    }
                }
            }
        }

        // Verificar si está dentro del horario del recinto
        $horarioInicio = $this->horarios_disponibles['inicio'] ?? '08:00';
        $horarioFin = $this->horarios_disponibles['fin'] ?? '23:00';
        
        if ($horaInicio < $horarioInicio || $horaFin > $horarioFin) {
            return false;
        }

        // Verificar conflictos con reservas APROBADAS o PENDIENTES
        $conflictos = $this->reservas()
            ->where('fecha_reserva', $fecha)
            ->whereIn('estado', ['aprobada', 'pendiente'])
            ->whereNull('fecha_cancelacion')
            ->where(function($query) use ($horaInicio, $horaFin) {
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

        return !$conflictos;
    }

    /**
     * Verifica si dos rangos de horarios se solapan
     */
    private function hayConflictoHorario($inicio1, $fin1, $inicio2, $fin2)
    {
        // Convertir a timestamps para comparación fácil
        $t1_inicio = strtotime($inicio1);
        $t1_fin = strtotime($fin1);
        $t2_inicio = strtotime($inicio2);
        $t2_fin = strtotime($fin2);

        // Hay conflicto si los rangos se solapan
        return ($t1_inicio < $t2_fin && $t1_fin > $t2_inicio);
    }

    /**
     * Obtener las reservas del día (aprobadas y no canceladas)
     */
    public function reservasDelDia($fecha)
    {
        return $this->reservas()
            ->where('fecha_reserva', $fecha)
            ->where('estado', 'aprobada')
            ->whereNull('fecha_cancelacion')
            ->orderBy('hora_inicio')
            ->get();
    }

    /**
     * Scope para obtener solo recintos activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * ⚠️ ACTUALIZADO: Obtener bloqueos para una fecha específica ⚠️
     */
    public function getBloqueosPorFecha($fecha)
    {
        $fechaString = Carbon::parse($fecha)->format('Y-m-d');
        $diasCerrados = $this->dias_cerrados;
        
        if (!is_array($diasCerrados) || !isset($diasCerrados['rangos_bloqueados'])) {
            return [];
        }

        $bloqueos = [];
        foreach ($diasCerrados['rangos_bloqueados'] as $bloqueo) {
            // Solo incluir bloqueos de esta fecha específica
            if (isset($bloqueo['fecha']) && $bloqueo['fecha'] === $fechaString) {
                $bloqueos[] = $bloqueo;
            }
        }

        return $bloqueos;
    }
}