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

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeProximos($query)
    {
        return $query->where('fecha_evento', '>=', now())
                     ->orderBy('fecha_evento', 'asc');
    }
}