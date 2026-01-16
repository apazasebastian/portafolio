<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo de Registro de Auditoria
 * 
 * Guarda un historial de todas las acciones importantes realizadas en el sistema.
 * Por ejemplo: cuando alguien aprueba una reserva, crea un recinto, reporta
 * una incidencia, etc. Esto permite saber quien hizo que y cuando.
 */
class AuditLog extends Model
{
    use HasFactory;

    /**
     * Campos que se guardan en cada registro de auditoria
     */
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

    /**
     * Conversion automatica de tipos de datos
     */
    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    // =========================================================================
    // RELACIONES - Conexiones con otras tablas
    // =========================================================================

    /**
     * Obtiene el usuario que realizo la accion
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtiene el elemento sobre el que se realizo la accion
     * Puede ser una reserva, un recinto, una incidencia, etc.
     */
    public function auditable()
    {
        return $this->morphTo();
    }

    // =========================================================================
    // METODO PRINCIPAL - Para registrar acciones
    // =========================================================================

    /**
     * Registra una nueva accion en el historial
     * 
     * Este metodo se llama desde cualquier parte del sistema cuando queremos
     * dejar registro de algo importante. Guarda automaticamente quien lo hizo,
     * desde que computador, y los detalles de la accion.
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

    // =========================================================================
    // SCOPES - Filtros reutilizables para buscar en el historial
    // =========================================================================

    /**
     * Filtra el historial por un tipo de accion especifico
     * Ejemplo: todas las aprobaciones de reservas
     */
    public function scopeOfAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Filtra el historial por un usuario especifico
     * Ejemplo: todas las acciones de Juan Perez
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Filtra el historial entre dos fechas
     * Ejemplo: todas las acciones del ultimo mes
     */
    public function scopeBetweenDates($query, $from, $to)
    {
        return $query->whereBetween('created_at', [$from, $to]);
    }

    // =========================================================================
    // ESTILOS - Colores e iconos para mostrar en la interfaz
    // =========================================================================

    /**
     * Obtiene los colores CSS para mostrar la accion en la interfaz
     * Cada tipo de accion tiene un color distintivo:
     * - Verde: acciones positivas (aprobar, crear, activar)
     * - Rojo: acciones negativas (rechazar, eliminar)
     * - Amarillo: cancelaciones
     * - Azul: ediciones
     */
    public function getActionColorAttribute()
    {
        return match($this->action) {
            // Reservas
            'aprobar_reserva' => 'bg-green-100 text-green-800 border border-green-200',
            'rechazar_reserva' => 'bg-red-100 text-red-800 border border-red-200',
            'cancelar_reserva' => 'bg-yellow-100 text-yellow-800 border border-yellow-200',
            'crear_reserva' => 'bg-blue-100 text-blue-800 border border-blue-200',
            
            // Recintos
            'crear_recinto' => 'bg-green-100 text-green-800 border border-green-200',
            'editar_recinto' => 'bg-blue-100 text-blue-800 border border-blue-200',
            'eliminar_recinto' => 'bg-red-100 text-red-800 border border-red-200',
            'activar_recinto' => 'bg-green-100 text-green-800 border border-green-200',
            'desactivar_recinto' => 'bg-gray-100 text-gray-800 border border-gray-200',
            
            // Eventos
            'crear_evento' => 'bg-green-100 text-green-800 border border-green-200',
            'editar_evento' => 'bg-blue-100 text-blue-800 border border-blue-200',
            'eliminar_evento' => 'bg-red-100 text-red-800 border border-red-200',
            'activar_evento' => 'bg-green-100 text-green-800 border border-green-200',
            'desactivar_evento' => 'bg-gray-100 text-gray-800 border border-gray-200',
            
            // Incidencias
            'crear_incidencia' => 'bg-orange-100 text-orange-800 border border-orange-200',
            'cambiar_estado_incidencia' => 'bg-blue-100 text-blue-800 border border-blue-200',
            'resolver_incidencia' => 'bg-green-100 text-green-800 border border-green-200',
            
            // Exportaciones
            'exportar_excel' => 'bg-green-100 text-green-800 border border-green-200',
            'exportar_pdf' => 'bg-red-100 text-red-800 border border-red-200',
            
            // Usuarios
            'crear_usuario' => 'bg-green-100 text-green-800 border border-green-200',
            'editar_usuario' => 'bg-blue-100 text-blue-800 border border-blue-200',
            'eliminar_usuario' => 'bg-red-100 text-red-800 border border-red-200',
            'cambiar_rol_usuario' => 'bg-purple-100 text-purple-800 border border-purple-200',
            
            // Sesiones
            'login' => 'bg-gray-100 text-gray-800 border border-gray-200',
            'logout' => 'bg-gray-100 text-gray-800 border border-gray-200',
            'acceso_denegado' => 'bg-red-100 text-red-800 border border-red-200',
            
            default => 'bg-gray-100 text-gray-800 border border-gray-200',
        };
    }

    /**
     * Obtiene un icono simple para representar cada tipo de accion
     * Se usan simbolos basicos para compatibilidad con todos los navegadores
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
            'crear_incidencia' => '!',
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
     * Obtiene el nombre de la accion en español y legible
     * Convierte codigos como "aprobar_reserva" en "Aprobar Reserva"
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
            
            // Usuarios (pendiente para segunda etapa)
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