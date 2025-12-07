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
     * Obtener clases CSS de Tailwind completas según la acción
     */
    public function getActionColorAttribute()
    {
        return match($this->action) {
            // Reservas - Verde (aprobadas)
            'aprobar_reserva' => 'bg-green-100 text-green-800 border border-green-200',
            // Reservas - Rojo (rechazadas)
            'rechazar_reserva' => 'bg-red-100 text-red-800 border border-red-200',
            // Reservas - Amarillo (canceladas)
            'cancelar_reserva' => 'bg-yellow-100 text-yellow-800 border border-yellow-200',
            // Reservas - Azul (creadas)
            'crear_reserva' => 'bg-blue-100 text-blue-800 border border-blue-200',
            
            // Recintos - Verde (crear)
            'crear_recinto' => 'bg-green-100 text-green-800 border border-green-200',
            // Recintos - Azul (editar)
            'editar_recinto' => 'bg-blue-100 text-blue-800 border border-blue-200',
            // Recintos - Rojo (eliminar)
            'eliminar_recinto' => 'bg-red-100 text-red-800 border border-red-200',
            // Recintos - Verde (activar)
            'activar_recinto' => 'bg-green-100 text-green-800 border border-green-200',
            // Recintos - Gris (desactivar)
            'desactivar_recinto' => 'bg-gray-100 text-gray-800 border border-gray-200',
            
            // Eventos - Verde (crear)
            'crear_evento' => 'bg-green-100 text-green-800 border border-green-200',
            // Eventos - Azul (editar)
            'editar_evento' => 'bg-blue-100 text-blue-800 border border-blue-200',
            // Eventos - Rojo (eliminar)
            'eliminar_evento' => 'bg-red-100 text-red-800 border border-red-200',
            // Eventos - Verde (activar)
            'activar_evento' => 'bg-green-100 text-green-800 border border-green-200',
            // Eventos - Gris (desactivar)
            'desactivar_evento' => 'bg-gray-100 text-gray-800 border border-gray-200',
            
            // Incidencias - Naranja (crear)
            'crear_incidencia' => 'bg-orange-100 text-orange-800 border border-orange-200',
            // Incidencias - Azul (cambiar estado)
            'cambiar_estado_incidencia' => 'bg-blue-100 text-blue-800 border border-blue-200',
            // Incidencias - Verde (resolver)
            'resolver_incidencia' => 'bg-green-100 text-green-800 border border-green-200',
            
            // Exportaciones - Verde (Excel)
            'exportar_excel' => 'bg-green-100 text-green-800 border border-green-200',
            // Exportaciones - Rojo (PDF)
            'exportar_pdf' => 'bg-red-100 text-red-800 border border-red-200',
            
            // Usuarios - Verde (crear)
            'crear_usuario' => 'bg-green-100 text-green-800 border border-green-200',
            // Usuarios - Azul (editar)
            'editar_usuario' => 'bg-blue-100 text-blue-800 border border-blue-200',
            // Usuarios - Rojo (eliminar)
            'eliminar_usuario' => 'bg-red-100 text-red-800 border border-red-200',
            // Usuarios - Púrpura (cambiar rol)
            'cambiar_rol_usuario' => 'bg-purple-100 text-purple-800 border border-purple-200',
            
            // Sesiones - Gris
            'login' => 'bg-gray-100 text-gray-800 border border-gray-200',
            'logout' => 'bg-gray-100 text-gray-800 border border-gray-200',
            'acceso_denegado' => 'bg-red-100 text-red-800 border border-red-200',
            
            // Por defecto - Gris
            default => 'bg-gray-100 text-gray-800 border border-gray-200',
        };
    }

    /**
     * ⚠️ SIN EMOJIS - Solo símbolos simples
     */
    public function getActionIconAttribute()
    {
        return match($this->action) {
            // Reservas
            'aprobar_reserva' => '✓',
            'rechazar_reserva' => '✗',
            'cancelar_reserva' => '⊗',
            'crear_reserva' => '+',
            
            // Recintos
            'crear_recinto' => '+',
            'editar_recinto' => '✎',
            'eliminar_recinto' => '✗',
            'activar_recinto' => '✓',
            'desactivar_recinto' => '⊗',
            
            // Eventos
            'crear_evento' => '+',
            'editar_evento' => '✎',
            'eliminar_evento' => '✗',
            'activar_evento' => '✓',
            'desactivar_evento' => '⊗',
            
            // Incidencias
            'crear_incidencia' => '⚠',
            'cambiar_estado_incidencia' => '↻',
            'resolver_incidencia' => '✓',
            
            // Exportaciones
            'exportar_excel' => '↓',
            'exportar_pdf' => '↓',
            
            // Usuarios
            'crear_usuario' => '+',
            'editar_usuario' => '✎',
            'eliminar_usuario' => '✗',
            'cambiar_rol_usuario' => '↻',
            
            // Sesiones
            'login' => '→',
            'logout' => '←',
            'acceso_denegado' => '✗',
            
            default => '•',
        };
    }

    /**
     * Obtener nombre legible de la acción
     */
    public function getActionNameAttribute()
    {
        return match($this->action) {
            // Reservas
            'aprobar_reserva' => 'Aprobar Reserva',
            'rechazar_reserva' => 'Rechazar Reserva',
            'cancelar_reserva' => 'Cancelar Reserva',
            'crear_reserva' => 'Crear Reserva',
            
            // Recintos
            'crear_recinto' => 'Crear Recinto',
            'editar_recinto' => 'Editar Recinto',
            'eliminar_recinto' => 'Eliminar Recinto',
            'activar_recinto' => 'Activar Recinto',
            'desactivar_recinto' => 'Desactivar Recinto',
            
            // Eventos
            'crear_evento' => 'Crear Evento',
            'editar_evento' => 'Editar Evento',
            'eliminar_evento' => 'Eliminar Evento',
            'activar_evento' => 'Activar Evento',
            'desactivar_evento' => 'Desactivar Evento',
            
            // Incidencias
            'crear_incidencia' => 'Crear Incidencia',
            'cambiar_estado_incidencia' => 'Cambiar Estado Incidencia',
            'resolver_incidencia' => 'Resolver Incidencia',
            
            // Exportaciones
            'exportar_excel' => 'Exportar Excel',
            'exportar_pdf' => 'Exportar PDF',
            
            // Usuarios () Queda para una segunda etapa, ya los usuarios se crean por el medio de php artisan.
            'crear_usuario' => 'Crear Usuario',
            'editar_usuario' => 'Editar Usuario',
            'eliminar_usuario' => 'Eliminar Usuario',
            'cambiar_rol_usuario' => 'Cambiar Rol Usuario',
            
            // Sesiones
            'login' => 'Inicio de Sesión',
            'logout' => 'Cierre de Sesión',
            'acceso_denegado' => 'Acceso Denegado',
            
            default => ucfirst(str_replace('_', ' ', $this->action)),
        };
    }
}