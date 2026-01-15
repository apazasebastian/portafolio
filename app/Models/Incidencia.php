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
        'tipo',
        'descripcion',
        'estado',
        'imagenes',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'imagenes' => 'array',
    ];

    // --- RELACIONES ---

    public function reserva()
    {
        return $this->belongsTo(Reserva::class);
    }
}