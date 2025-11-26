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

    // --- RELACIONES ---

    public function reservas()
    {
        return $this->hasMany(Reserva::class, 'nombre_organizacion', 'nombre');
    }
}