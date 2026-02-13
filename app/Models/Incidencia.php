<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incidencia extends Model
{
    use HasFactory;

    protected $table = 'incidencias';

    protected $fillable = [
        'reserva_id',
        'recinto_id',
        'tipo',
        'descripcion',
        'estado',
        'imagenes',
        'reportado_por',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'imagenes' => 'array',
    ];

    // =========================================================================
    // RELACIONES - Conexiones con otras tablas de la base de datos
    // =========================================================================

    /**
     * Obtiene la reserva asociada a esta incidencia
     */
    public function reserva(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Reserva::class);
    }

    /**
     * Obtiene el recinto directamente (para informes sin reserva)
     */
    public function recinto(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Recinto::class);
    }

    // =========================================================================
    // SCOPES - Filtros reutilizables para consultas
    // =========================================================================

    /**
     * Filtra incidencias pendientes (sin resolver)
     */
    public function scopePendientes(\Illuminate\Database\Eloquent\Builder $query): void
    {
        $query->where('estado', 'pendiente');
    }

    /**
     * Filtra incidencias resueltas
     */
    public function scopeResueltas(\Illuminate\Database\Eloquent\Builder $query): void
    {
        $query->where('estado', 'resuelta');
    }

    /**
     * Filtra incidencias por tipo específico
     */
    public function scopePorTipo(\Illuminate\Database\Eloquent\Builder $query, string $tipo): void
    {
        $query->where('tipo', $tipo);
    }

    // =========================================================================
    // ACCESSORS - Propiedades calculadas automáticamente
    // =========================================================================

    /**
     * Obtiene el estado en formato legible
     */
    protected function estadoLegible(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn () => match($this->estado) {
                'pendiente' => 'Pendiente de Revisión',
                'resuelta' => 'Resuelta',
                'en_proceso' => 'En Proceso',
                default => ucfirst($this->estado)
            }
        );
    }
}