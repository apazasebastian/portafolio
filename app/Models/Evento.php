<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'descripcion',
        'fecha_evento',
        'imagen_url',
        'activo',
        'enlace_externo'
    ];

    protected $casts = [
        'fecha_evento' => 'datetime',
        'activo' => 'boolean'
    ];

    // =========================================================================
    // SCOPES - Filtros reutilizables para consultas
    // =========================================================================

    /**
     * Filtra solo los eventos activos
     */
    public function scopeActivos(\Illuminate\Database\Eloquent\Builder $query): void
    {
        $query->where('activo', true);
    }

    /**
     * Filtra eventos próximos (futuros)
     */
    public function scopeProximos(\Illuminate\Database\Eloquent\Builder $query): void
    {
        $query->where('fecha_evento', '>=', now())
              ->orderBy('fecha_evento', 'asc');
    }

    /**
     * Filtra eventos que ya ocurrieron
     */
    public function scopePasados(\Illuminate\Database\Eloquent\Builder $query): void
    {
        $query->where('fecha_evento', '<', now())
              ->orderBy('fecha_evento', 'desc');
    }
}
