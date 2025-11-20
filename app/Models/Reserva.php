<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Str; // Necesario para generar el código aleatorio

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
        // Nuevos campos para la cancelación
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
        // Casts para los nuevos campos
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

    // --- ACCESORES Y FORMATEO ---

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
        
        return $inicio->diffInHours($fin) . ' horas';
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

    public function scopeFuturas($query)
    {
        return $query->where('fecha_reserva', '>=', now()->toDateString());
    }

    // --- LÓGICA DE NEGOCIO ---

    public function esEditable()
    {
        return $this->estado === 'pendiente' && 
               $this->fecha_reserva > now()->toDateString();
    }

    public function aprobar($userId, $observaciones = null)
    {
        $this->update([
            'estado' => 'aprobada',
            'aprobada_por' => $userId,
            'fecha_respuesta' => now(),
            'observaciones' => $observaciones
        ]);
    }

    public function rechazar($userId, $motivo)
    {
        $this->update([
            'estado' => 'rechazada',
            'aprobada_por' => $userId,
            'fecha_respuesta' => now(),
            'motivo_rechazo' => $motivo
        ]);
    }

    // --- FUNCIONES DE CANCELACIÓN ---

    /**
     * Busca una reserva por su código único de cancelación.
     */
    public static function buscarPorCodigo($codigo)
    {
        return self::where('codigo_cancelacion', $codigo)->first();
    }

    /**
     * Verifica si la reserva cumple las condiciones para ser cancelada.
     */
    public function esCancelable()
    {
        // 1. No se puede cancelar si ya fue cancelada
        if (!is_null($this->fecha_cancelacion)) {
            return false;
        }

        // 2. No se puede cancelar si ya fue rechazada por administración
        if ($this->estado === 'rechazada') {
            return false;
        }

        // 3. Validar que la fecha/hora de la reserva sea en el futuro
        try {
            // Creamos una copia para no modificar la fecha original
            $fechaHoraInicio = $this->fecha_reserva->copy()->setTimeFrom($this->hora_inicio);
            return $fechaHoraInicio->isFuture();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Ejecuta la cancelación de la reserva.
     */
    public function cancelar($motivo = null, $canceladaPorUsuario = true)
    {
        $this->update([
            'fecha_cancelacion' => now(),
            'motivo_cancelacion' => $motivo,
            'cancelada_por_usuario' => $canceladaPorUsuario,
            // Opcional: Si quieres liberar el cupo visualmente, puedes descomentar la siguiente línea
            // 'estado' => 'rechazada' 
        ]);
    }

    /**
     * Cancela la reserva iniciada por el usuario
     */
    public function cancelarPorUsuario($motivo = null)
    {
        return $this->cancelar($motivo, true);
    }

    // --- GENERACIÓN AUTOMÁTICA DE CÓDIGO ---

    protected static function boot()
    {
        parent::boot();

        // Genera el código automáticamente ANTES de crear una reserva nueva
        static::creating(function ($reserva) {
            if (empty($reserva->codigo_cancelacion)) {
                // Genera un código con formato: XXXXXXXX-XXXXXXXX (8 caracteres - guión - 8 caracteres)
                $parte1 = Str::upper(Str::random(8));
                $parte2 = Str::upper(Str::random(8));
                $reserva->codigo_cancelacion = $parte1 . '-' . $parte2;
            }
        });
    }
}