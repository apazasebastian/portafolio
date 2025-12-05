<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_name',
        'user_email',
        'user_role',
        'action',
        'description',
        'auditable_type',
        'auditable_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Relación con el usuario que realizó la acción
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación polimórfica con el modelo auditado
     */
    public function auditable()
    {
        return $this->morphTo();
    }

    /**
     * Registrar una acción en el log de auditoría
     */
    public static function log($action, $description, $auditable = null, $oldValues = null, $newValues = null)
    {
        return self::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name ?? 'Sistema',
            'user_email' => auth()->user()->email ?? 'sistema@municipalidadarica.cl',
            'user_role' => auth()->user()->role ?? 'sistema',
            'action' => $action,
            'description' => $description,
            'auditable_type' => $auditable ? get_class($auditable) : null,
            'auditable_id' => $auditable?->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Scope para filtrar por acción
     */
    public function scopeOfAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope para filtrar por usuario
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope para filtrar por fecha
     */
    public function scopeBetweenDates($query, $from, $to)
    {
        return $query->whereBetween('created_at', [$from, $to]);
    }

    /**
     * Obtener color del badge según la acción
     */
    public function getActionColorAttribute()
    {
        return match($this->action) {
            'aprobar_reserva' => 'green',
            'rechazar_reserva' => 'red',
            'cancelar_reserva' => 'yellow',
            'crear_incidencia' => 'orange',
            'cambiar_estado_incidencia' => 'blue',
            'login' => 'gray',
            'logout' => 'gray',
            default => 'gray',
        };
    }

    /**
     * Obtener icono según la acción
     */
    public function getActionIconAttribute()
    {
        return match($this->action) {
            'aprobar_reserva' => '✓',
            'rechazar_reserva' => '✗',
            'cancelar_reserva' => '⊗',
            'crear_incidencia' => '⚠',
            'cambiar_estado_incidencia' => '↻',
            'login' => '→',
            'logout' => '←',
            default => '•',
        };
    }
}