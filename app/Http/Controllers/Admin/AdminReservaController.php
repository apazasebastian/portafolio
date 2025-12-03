<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reserva;
use App\Models\Recinto; // Importante para los filtros * Esto sera para la segunda etapa, pero fue agregado de todas formas
use App\Mail\ReservaAprobada;
use App\Mail\ReservaRechazada;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class AdminReservaController extends Controller
{
    /**
     * Mostrar listado de reservas con filtros activos, aun falta el de cancelada, pendiente
     */
    public function index(Request $request)
    {
        // Iniciamos la consulta cargando la relación con el recinto
        $query = Reserva::with(['recinto']);

        // 1. Filtro por Estado (Pendiente, Aprobada, Rechazada, Cancelada) /El cancelada aun no funciona, falta agregar, esta pendiente
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // 2. Filtro por Recinto
        if ($request->filled('recinto_id')) {
            $query->where('recinto_id', $request->recinto_id);
        }

        // 3. Filtro por Fecha específica
        if ($request->filled('fecha')) {
            $query->whereDate('fecha_reserva', $request->fecha);
        }

        // Ordenamos por fecha descendente y paginamos
        // withQueryString() mantiene los filtros en la URL al cambiar de página
        $reservas = $query->orderBy('fecha_reserva', 'desc')
            ->paginate(15)
            ->withQueryString();
        
        // Cargamos los recintos para el menú desplegable del filtro
        $recintos = Recinto::all();
        
        return view('admin.reservas.index', compact('reservas', 'recintos'));
    }

    /**
     * Mostrar detalles de una reserva específica.
     */
    public function show(Reserva $reserva)
    {
        // Cargar relaciones necesarias para la vista
        $reserva->load(['recinto', 'aprobadaPor']);
        
        return view('admin.reservas.show', compact('reserva'));
    }

    /**
     * Aprobar una reserva.
     */
    public function aprobar(Reserva $reserva)
    {
        // Generar código de cancelación si no existe
        if (!$reserva->codigo_cancelacion) {
            $reserva->codigo_cancelacion = $this->generarCodigoCancelacion();
        }
        
        // Actualizar estado
        $reserva->estado = 'aprobada';
        $reserva->fecha_respuesta = now();
        $reserva->aprobada_por = auth()->id();
        $reserva->save();
        
        // Enviar correo de aprobación
        try {
            Mail::to($reserva->email)->send(new ReservaAprobada($reserva));
        } catch (\Exception $e) {
            \Log::error('Error enviando correo de aprobación: ' . $e->getMessage());
        }
        
        return redirect()->route('admin.reservas.show', $reserva)
            ->with('success', 'Reserva aprobada correctamente y correo enviado.');
    }

    /**
     * Rechazar una reserva.
     */
    public function rechazar(Request $request, Reserva $reserva)
    {
        $request->validate([
            'motivo_rechazo' => 'required|string|max:500'
        ]);
        
        $reserva->estado = 'rechazada';
        $reserva->fecha_respuesta = now();
        $reserva->motivo_rechazo = $request->motivo_rechazo;
        $reserva->save();
        
        // Enviar correo de rechazo
        try {
            Mail::to($reserva->email)->send(new ReservaRechazada($reserva));
        } catch (\Exception $e) {
            \Log::error('Error enviando correo de rechazo: ' . $e->getMessage());
        }
        
        return redirect()->route('admin.reservas.show', $reserva)
            ->with('success', 'Reserva rechazada y notificación enviada.');
    }

    /**
     * Genera un código único de cancelación.
     */
    private function generarCodigoCancelacion()
    {
        do {
            $codigo = strtoupper(Str::random(8) . '-' . Str::random(8));
        } while (Reserva::where('codigo_cancelacion', $codigo)->exists());
        
        return $codigo;
    }
}