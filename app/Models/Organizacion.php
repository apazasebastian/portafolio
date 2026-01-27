<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organizacion extends Model
{
    use HasFactory;

    protected $table = 'organizaciones';

    protected $fillable = [
        'nombre',
        'rut',
        'email',
        'telefono',
        'descripcion',
        'estado',
    ];

    /**
     * Conversión automática de tipos de datos
     */
    protected $casts = [
        'rut' => \App\Casts\RutChileno::class,
    ];

    // =========================================================================
    // RELACIONES - Conexiones con otras tablas de la base de datos
    // =========================================================================

    /**
     * Obtiene todas las reservas de esta organización
     */
    public function reservas(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Reserva::class, 'nombre_organizacion', 'nombre');
    }

    // =========================================================================
    // SCOPES - Filtros reutilizables para consultas
    // =========================================================================

    /**
     * Filtra solo organizaciones activas
     */
    public function scopeActivas(\Illuminate\Database\Eloquent\Builder $query): void
    {
        $query->where('estado', 'activa');
    }

    /**
     * Filtra organizaciones que tienen al menos una reserva
     */
    public function scopeConReservas(\Illuminate\Database\Eloquent\Builder $query): void
    {
        $query->has('reservas');
    }

    // =========================================================================
    // ACCESSORS - Propiedades calculadas automáticamente
    // =========================================================================

    /**
     * Obtiene el RUT formateado con puntos y guión
     * El cast RutChileno ya se encarga del formato automáticamente
     */
    protected function rutFormateado(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn () => $this->rut  // El cast ya lo formatea
        );
    }
}