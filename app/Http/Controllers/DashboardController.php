<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Recinto;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Estadísticas generales
        $reservasPendientes = Reserva::where('estado', 'pendiente')->count();
        $reservasHoy = Reserva::whereDate('fecha_reserva', today())->count();
        $reservasEstesMes = Reserva::whereMonth('fecha_reserva', now()->month)
            ->whereYear('fecha_reserva', now()->year)
            ->count();
        $recintosActivos = Recinto::where('activo', true)->count();

        // Contadores por estado
        $todasReservas = Reserva::count();
        $contadorAprobadas = Reserva::where('estado', 'aprobada')->count();
        $contadorRechazadas = Reserva::where('estado', 'rechazada')->count();
        $contadorCanceladas = Reserva::whereNotNull('fecha_cancelacion')->count();

        // Query base para reservas
        $query = Reserva::with(['recinto']);

        // Filtro por pestaña (todas, pendientes, aprobadas, rechazadas, canceladas)
        $filtro = $request->get('filtro', 'todas');
        
        if ($filtro === 'pendientes') {
            $query->where('estado', 'pendiente');
        } elseif ($filtro === 'aprobadas') {
            $query->where('estado', 'aprobada');
        } elseif ($filtro === 'rechazadas') {
            $query->where('estado', 'rechazada');
        } elseif ($filtro === 'canceladas') {
            $query->whereNotNull('fecha_cancelacion');
        }

        // Filtros avanzados
        if ($request->filled('recinto_id')) {
            $query->where('recinto_id', $request->recinto_id);
        }

        if ($request->filled('deporte')) {
            $query->where('deporte', $request->deporte);
        }

        if ($request->filled('fecha')) {
            $query->whereDate('fecha_reserva', $request->fecha);
        }

        // Ordenar y paginar
        $reservas = $query->orderBy('fecha_reserva', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Obtener recintos para el filtro
        $recintos = Recinto::all();

        //  DATOS PARA EL CALENDARIO INTERACTIVO (NUEVO) 
        $todasReservasCalendario = Reserva::with('recinto')
            ->select('id', 'recinto_id', 'nombre_organizacion', 'fecha_reserva', 'hora_inicio', 'hora_fin', 'estado')
            ->whereYear('fecha_reserva', now()->year)
            ->get()
            ->map(function($reserva) {
                return [
                    'id' => $reserva->id,
                    'organizacion' => $reserva->nombre_organizacion,
                    'recinto' => $reserva->recinto->nombre ?? 'N/A',
                    'fecha' => $reserva->fecha_reserva->format('Y-m-d'),
                    'hora_inicio' => \Carbon\Carbon::parse($reserva->hora_inicio)->format('H:i'),
                    'hora_fin' => \Carbon\Carbon::parse($reserva->hora_fin)->format('H:i'),
                    'estado' => $reserva->estado
                ];
            });

        return view('admin.dashboard', compact(
            'reservasPendientes',
            'reservasHoy',
            'reservasEstesMes',
            'recintosActivos',
            'todasReservas',
            'contadorAprobadas',
            'contadorRechazadas',
            'contadorCanceladas',
            'reservas',
            'recintos',
            'todasReservasCalendario'
        ));
    }

    /**
     * Exportar reservas del dashboard a Excel (CSV)
     */
    public function exportar(Request $request)
    {
        try {
            // Construir query con los mismos filtros del dashboard
            $query = Reserva::with(['recinto']);

            // Aplicar filtros
            $filtro = $request->get('filtro', 'todas');
            
            if ($filtro === 'pendientes') {
                $query->where('estado', 'pendiente');
            } elseif ($filtro === 'aprobadas') {
                $query->where('estado', 'aprobada');
            } elseif ($filtro === 'rechazadas') {
                $query->where('estado', 'rechazada');
            } elseif ($filtro === 'canceladas') {
                $query->whereNotNull('fecha_cancelacion');
            }

            if ($request->filled('recinto_id')) {
                $query->where('recinto_id', $request->recinto_id);
            }

            if ($request->filled('deporte')) {
                $query->where('deporte', $request->deporte);
            }

            if ($request->filled('fecha')) {
                $query->whereDate('fecha_reserva', $request->fecha);
            }

            $reservas = $query->orderBy('fecha_reserva', 'desc')->get();

            $csvFileName = 'reservas_' . Carbon::now()->format('d-m-Y-His') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="' . $csvFileName . '"',
            ];

            $callback = function () use ($reservas) {
                $file = fopen('php://output', 'w');
                
                // BOM para UTF-8
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                
                // Encabezados
                fputcsv($file, [
                    'ID',
                    'Organización',
                    'Representante',
                    'Email',
                    'Teléfono',
                    'RUT',
                    'Recinto',
                    'Deporte',
                    'Fecha',
                    'Hora Inicio',
                    'Hora Fin',
                    'Personas',
                    'Estado',
                    'Fecha Aprobación/Rechazo',
                    'Motivo Rechazo',
                    'Cancelada'
                ], ',');

                // Datos
                foreach ($reservas as $reserva) {
                    fputcsv($file, [
                        $reserva->id,
                        $reserva->nombre_organizacion ?? 'N/A',
                        $reserva->representante_nombre,
                        $reserva->email,
                        $reserva->telefono,
                        $reserva->rut,
                        $reserva->recinto->nombre ?? 'N/A',
                        $reserva->deporte,
                        $reserva->fecha_reserva->format('d/m/Y'),
                        \Carbon\Carbon::parse($reserva->hora_inicio)->format('H:i'),
                        \Carbon\Carbon::parse($reserva->hora_fin)->format('H:i'),
                        $reserva->cantidad_personas,
                        ucfirst($reserva->estado),
                        $reserva->fecha_respuesta ? $reserva->fecha_respuesta->format('d/m/Y H:i') : 'N/A',
                        $reserva->motivo_rechazo ?? 'N/A',
                        $reserva->fecha_cancelacion ? 'Sí' : 'No'
                    ], ',');
                }
                
                fclose($file);
            };

            return response()->streamDownload($callback, $csvFileName, $headers);

        } catch (\Exception $e) {
            return back()->with('error', 'Error al exportar reservas: ' . $e->getMessage());
        }
    }
}