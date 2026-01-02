<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    // Usa el trait HasFactory para crear usuarios de prueba con factories
    // Usa el trait Notifiable para enviar notificaciones (emails, SMS, etc.)
    use HasFactory, Notifiable;

    /**
     * Los atributos que se pueden asignar masivamente.
     * Esto significa que puedo hacer: User::create(['name' => '...', 'email' => '...'])
     * Sin estos, Laravel rechazaría la asignación por seguridad
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',        // Nombre del usuario
        'email',       // Email del usuario
        'password',    // Contraseña del usuario
        'role',        // Rol del usuario (jefe_recintos, encargado_recinto, etc)
    ];

    /**
     * Los atributos que deben ocultarse en la serialización.
     * Cuando conviertes el usuario a JSON, estos campos NO aparecen
     * (por seguridad, para no exponer la contraseña en respuestas HTTP)
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',          // Ocultar la contraseña
        'remember_token',    // Token de "recuérdame" en cookies
    ];

    /**
     * Obtener los atributos que deben ser convertidos.
     * Define cómo Laravel debe procesar ciertos campos
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            // Convertir 'email_verified_at' a objeto Carbon (fecha y hora)
            'email_verified_at' => 'datetime',
            
            // Convertir 'password' automáticamente a hash bcrypt
            // Cuando asignas una contraseña, Laravel la encripta automáticamente
            'password' => 'hashed',
        ];
    }
}