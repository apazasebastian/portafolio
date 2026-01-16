<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Str;

/**
 * Modelo de Reserva
 * 
 * Representa una solicitud de reserva de un recinto deportivo.
 * Contiene toda la informacion del solicitante (organizacion, representante),
 * el recinto que quieren usar, la fecha y horario solicitado, y el estado
 * de la reserva (pendiente, aprobada, rechazada o cancelada).
 */
class Reserva extends Model
{
    use HasFactory;

    /**
     * Campos que se pueden llenar al crear o actualizar una reserva
     */
    protected $fillable = [
        'recinto_id',
        'deporte',
        'rut',
        'nombre_organizacion',
        'representante_nombre',
        'email',
        'email_confirmacion',
        'telefono',
        'direccion',
        'region',
        'comuna',
        'cantidad_personas',
        'fecha_reserva',
        'hora_inicio',
        'hora_fin',
        'estado',
        'observaciones',
        'motivo_rechazo',
        'acepta_reglamento',
        'codigo_cancelacion',
        'fecha_cancelacion',
        'cancelada_por',
        'motivo_cancelacion',
        'cancelada_por_usuario'
    ];

    /**
     * Conversion automatica de tipos de datos
     * Por ejemplo, fecha_reserva siempre sera un objeto de fecha
     */
    protected $casts = [
        'fecha_reserva' => 'date',
        'hora_inicio' => 'datetime:H:i',
        'hora_fin' => 'datetime:H:i',
        'fecha_respuesta' => 'datetime',
        'acepta_reglamento' => 'boolean',
        'fecha_cancelacion' => 'datetime',
        'cancelada_por_usuario' => 'boolean'
    ];

    // =========================================================================
    // RELACIONES - Conexiones con otras tablas de la base de datos
    // =========================================================================

    /**
     * Obtiene el recinto donde se hara la reserva
     */
    public function recinto()
    {
        return $this->belongsTo(Recinto::class);
    }

    /**
     * Obtiene el usuario administrador que aprobo o rechazo la reserva
     */
    public function aprobadaPor()
    {
        return $this->belongsTo(User::class, 'aprobada_por');
    }

    /**
     * Obtiene la organizacion asociada a esta reserva
     */
    public function organizacion()
    {
        return $this->belongsTo(Organizacion::class, 'nombre_organizacion', 'nombre');
    }

    /**
     * Obtiene todas las incidencias reportadas despues de esta reserva
     */
    public function incidencias()
    {
        return $this->hasMany(Incidencia::class);
    }

    // =========================================================================
    // SCOPES - Filtros reutilizables para consultas
    // =========================================================================

    /**
     * Filtra solo las reservas que estan esperando revision
     */
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    /**
     * Filtra solo las reservas que fueron aprobadas
     */
    public function scopeAprobadas($query)
    {
        return $query->where('estado', 'aprobada');
    }

    /**
     * Filtra solo las reservas que fueron rechazadas
     */
    public function scopeRechazadas($query)
    {
        return $query->where('estado', 'rechazada');
    }

    // =========================================================================
    // ACCESORES - Propiedades calculadas automaticamente
    // =========================================================================

    /**
     * Devuelve el RUT con formato chileno (puntos y guion)
     * Ejemplo: "12345678-9" en lugar de "123456789"
     */
    public function getRutFormateadoAttribute()
    {
        $rut = $this->rut;
        if (strlen($rut) < 8) return $rut;
        
        $cuerpo = substr($rut, 0, -1);
        $dv = substr($rut, -1);
        
        return number_format($cuerpo, 0, '', '.') . '-' . $dv;
    }

    /**
     * Calcula cuanto dura la reserva (diferencia entre hora inicio y fin)
     * Ejemplo: "2 horas"
     */
    public function getDuracionAttribute()
    {
        $inicio = Carbon::parse($this->hora_inicio);
        $fin = Carbon::parse($this->hora_fin);
        
        $horas = $inicio->diffInHours($fin);
        return $horas . ' hora' . ($horas != 1 ? 's' : '');
    }

    // =========================================================================
    // METODOS DE VERIFICACION - Para saber el estado de la reserva
    // =========================================================================

    /**
     * Verifica si la reserva ya termino (la fecha y hora de fin ya pasaron)
     * Se usa para saber si ya se puede reportar una incidencia
     */
    public function haFinalizado()
    {
        if ($this->estado !== 'aprobada') {
            return false;
        }

        try {
            $fechaHoraFin = $this->fecha_reserva->copy()->setTimeFrom($this->hora_fin);
            return $fechaHoraFin->isPast();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Verifica si se puede reportar una incidencia para esta reserva
     * Solo es posible si la reserva fue aprobada y ya termino
     */
    public function puedeReportarIncidencia()
    {
        return $this->estado === 'aprobada' && $this->haFinalizado();
    }

    /**
     * Verifica si la reserva tiene incidencias reportadas
     */
    public function tieneIncidencias()
    {
        return $this->incidencias()->count() > 0;
    }

    /**
     * Cuenta cuantas incidencias se han reportado para esta reserva
     */
    public function cantidadIncidencias()
    {
        return $this->incidencias()->count();
    }

    /**
     * Busca una reserva usando el codigo de cancelacion
     * Este codigo se entrega al ciudadano cuando su reserva es aprobada
     */
    public static function buscarPorCodigo($codigo)
    {
        return static::where('codigo_cancelacion', $codigo)->first();
    }

    /**
     * Verifica si la reserva puede ser cancelada
     * 
     * Una reserva puede cancelarse si:
     * - No ha sido cancelada previamente
     * - No fue rechazada
     * - La fecha y hora de inicio aun no han pasado
     */
    public function puedeCancelar()
    {
        // Si ya fue cancelada, no se puede cancelar de nuevo
        if ($this->fecha_cancelacion) {
            return false;
        }

        // Las reservas rechazadas no se pueden cancelar
        if ($this->estado === 'rechazada') {
            return false;
        }

        // Solo se puede cancelar si la reserva aun no ha comenzado
        try {
            $fechaHoraInicio = $this->fecha_reserva->copy()->setTimeFrom($this->hora_inicio);
            return $fechaHoraInicio->isFuture();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Nombre alternativo para puedeCancelar()
     * Existe por compatibilidad con codigo anterior
     */
    public function esCancelable()
    {
        return $this->puedeCancelar();
    }

    /**
     * Cancela esta reserva
     * Guarda la fecha de cancelacion y el motivo
     */
    public function cancelar($motivo = null, $canceladaPorUsuario = true)
    {
        $this->update([
            'fecha_cancelacion' => now(),
            'motivo_cancelacion' => $motivo,
            'cancelada_por_usuario' => $canceladaPorUsuario,
        ]);
    }

    /**
     * Cancela la reserva indicando que fue el ciudadano quien la cancelo
     */
    public function cancelarPorUsuario($motivo = null)
    {
        return $this->cancelar($motivo, true);
    }

    // =========================================================================
    // EVENTOS DEL MODELO - Acciones automaticas al crear o modificar
    // =========================================================================

    protected static function boot()
    {
        parent::boot();

        // Cuando se crea una nueva reserva, se genera automaticamente
        // el codigo de cancelacion que se entregara al ciudadano
        static::creating(function ($reserva) {
            if (empty($reserva->codigo_cancelacion)) {
                $parte1 = Str::upper(Str::random(8));
                $parte2 = Str::upper(Str::random(8));
                $reserva->codigo_cancelacion = $parte1 . '-' . $parte2;
            }
        });
    }
}