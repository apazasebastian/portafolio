<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Str;

class Reserva extends Model
{
    use HasFactory;

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

    protected $casts = [
        'fecha_reserva' => 'date',
        'hora_inicio' => 'datetime:H:i',
        'hora_fin' => 'datetime:H:i',
        'fecha_respuesta' => 'datetime',
        'acepta_reglamento' => 'boolean',
        'fecha_cancelacion' => 'datetime',
        'cancelada_por_usuario' => 'boolean'
    ];

    // --- RELACIONES ---

    public function recinto()
    {
        return $this->belongsTo(Recinto::class);
    }

    public function aprobadaPor()
    {
        return $this->belongsTo(User::class, 'aprobada_por');
    }

    public function organizacion()
    {
        return $this->belongsTo(Organizacion::class, 'nombre_organizacion', 'nombre');
    }

    public function incidencias()
    {
        return $this->hasMany(Incidencia::class);
    }

    // --- SCOPES ---

    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeAprobadas($query)
    {
        return $query->where('estado', 'aprobada');
    }

    public function scopeRechazadas($query)
    {
        return $query->where('estado', 'rechazada');
    }

    // --- ACCESORES ---

    public function getRutFormateadoAttribute()
    {
        $rut = $this->rut;
        if (strlen($rut) < 8) return $rut;
        
        $cuerpo = substr($rut, 0, -1);
        $dv = substr($rut, -1);
        
        return number_format($cuerpo, 0, '', '.') . '-' . $dv;
    }

    public function getDuracionAttribute()
    {
        $inicio = Carbon::parse($this->hora_inicio);
        $fin = Carbon::parse($this->hora_fin);
        
        $horas = $inicio->diffInHours($fin);
        return $horas . ' hora' . ($horas != 1 ? 's' : '');
    }

    // --- MÉTODOS DE VALIDACIÓN ---

    /**
     * Verifica si la reserva ya finalizó (fecha y hora de término pasaron)
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
     * Verifica si se puede reportar incidencia
     * (reserva finalizada y aprobada)
     */
    public function puedeReportarIncidencia()
    {
        return $this->estado === 'aprobada' && $this->haFinalizado();
    }

    /**
     * Verifica si ya tiene incidencias reportadas
     */
    public function tieneIncidencias()
    {
        return $this->incidencias()->count() > 0;
    }

    /**
     * Obtiene la cantidad de incidencias reportadas
     */
    public function cantidadIncidencias()
    {
        return $this->incidencias()->count();
    }

    /**
     * Buscar reserva por código de cancelación
     */
    public static function buscarPorCodigo($codigo)
    {
        return static::where('codigo_cancelacion', $codigo)->first();
    }

    public function puedeCancelar()
    {
        if ($this->fecha_cancelacion) {
            return false;
        }

        if ($this->estado === 'rechazada') {
            return false;
        }

        try {
            $fechaHoraInicio = $this->fecha_reserva->copy()->setTimeFrom($this->hora_inicio);
            return $fechaHoraInicio->isFuture();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Alias de puedeCancelar() para compatibilidad
     */
    public function esCancelable()
    {
        return $this->puedeCancelar();
    }

    public function cancelar($motivo = null, $canceladaPorUsuario = true)
    {
        $this->update([
            'fecha_cancelacion' => now(),
            'motivo_cancelacion' => $motivo,
            'cancelada_por_usuario' => $canceladaPorUsuario,
        ]);
    }

    public function cancelarPorUsuario($motivo = null)
    {
        return $this->cancelar($motivo, true);
    }

    // --- GENERACIÓN AUTOMÁTICA DE CÓDIGO ---

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($reserva) {
            if (empty($reserva->codigo_cancelacion)) {
                $parte1 = Str::upper(Str::random(8));
                $parte2 = Str::upper(Str::random(8));
                $reserva->codigo_cancelacion = $parte1 . '-' . $parte2;
            }
        });
    }
}