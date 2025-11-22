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

    public function disponibleEn($fecha, $horaInicio, $horaFin)
    {
        $fechaCarbon = Carbon::parse($fecha);
        $diaSemana = strtolower($fechaCarbon->format('l'));

        // Convertir dias_cerrados a array si es necesario
        $diasCerrados = $this->dias_cerrados;
        if (is_string($diasCerrados)) {
            $diasCerrados = json_decode($diasCerrados, true) ?? [];
        } elseif (!is_array($diasCerrados)) {
            $diasCerrados = [];
        }

        // Verificar si es día cerrado
        if (in_array($diaSemana, $diasCerrados)) {
            return false;
        }

        $horarioInicio = $this->horarios_disponibles['inicio'] ?? '08:00';
        $horarioFin = $this->horarios_disponibles['fin'] ?? '23:00';
        
        if ($horaInicio < $horarioInicio || $horaFin > $horarioFin) {
            return false;
        }

        $conflictos = $this->reservas()
            ->where('fecha_reserva', $fecha)
            ->where('estado', 'aprobada')
            ->whereNull('fecha_cancelacion') // Excluir reservas canceladas
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

    public function reservasDelDia($fecha)
    {
        return $this->reservas()
            ->where('fecha_reserva', $fecha)
            ->where('estado', 'aprobada')
            ->whereNull('fecha_cancelacion') // Excluir reservas canceladas
            ->orderBy('hora_inicio')
            ->get();
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}