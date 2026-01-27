<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // =========================================================================
    // RELACIONES - Conexiones con otras tablas de la base de datos
    // =========================================================================

    /**
     * Obtiene las reservas que este usuario aprobó o rechazó
     */
    public function reservasAprobadas(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\Reserva::class, 'aprobada_por');
    }

    /**
     * Obtiene el recinto del cual este usuario es encargado
     */
    public function recintoAsignado(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Recinto::class, 'recinto_asignado_id');
    }

    // =========================================================================
    // SCOPES - Filtros reutilizables para consultas
    // =========================================================================

    /**
     * Filtra solo los usuarios administradores
     */
    public function scopeAdmins(\Illuminate\Database\Eloquent\Builder $query): void
    {
        $query->where('is_admin', true);
    }

    /**
     * Filtra solo los encargados de recintos
     */
    public function scopeEncargados(\Illuminate\Database\Eloquent\Builder $query): void
    {
        $query->whereNotNull('recinto_asignado_id');
    }

    /**
     * Filtra encargados asignados a un recinto específico
     */
    public function scopeAsignadosARecinto(\Illuminate\Database\Eloquent\Builder $query, int $recintoId): void
    {
        $query->where('recinto_asignado_id', $recintoId);
    }
}
