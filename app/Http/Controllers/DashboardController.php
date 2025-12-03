<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Recinto;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Estadísticas básicas (sin filtros)
        $reservasPendientes = Reserva::pendientes()->count();
        $reservasHoy = Reserva::aprobadas()
            ->where('fecha_reserva', Carbon::today())
            ->count();
        
        $reservasEstesMes = Reserva::aprobadas()
            ->whereMonth('fecha_reserva', Carbon::now()->month)
            ->count();
            
        $recintosActivos = Recinto::activos()->count();

        // Contadores por estado (para las pestañas)
        $todasReservas = Reserva::count();
        $contadorAprobadas = Reserva::where('estado', 'aprobada')->count();
        $contadorRechazadas = Reserva::where('estado', 'rechazada')->count();
        $contadorCanceladas = Reserva::whereNotNull('fecha_cancelacion')->count();
        
        // Query para el listado de reservas con filtros
        $query = Reserva::with(['recinto']);

        // 1. Filtro por pestaña/estado rápido
        $filtro = $request->get('filtro', 'todas');
        
        switch ($filtro) {
            case 'pendientes':
                $query->where('estado', 'pendiente');
                break;
            case 'aprobadas':
                $query->where('estado', 'aprobada');
                break;
            case 'rechazadas':
                $query->where('estado', 'rechazada');
                break;
            case 'canceladas':
                $query->whereNotNull('fecha_cancelacion');
                break;
            case 'todas':
            default:
                // Mostrar todas, no aplicar filtro de estado
                break;
        }

        // 2. Filtro por Recinto
        if ($request->filled('recinto_id')) {
            $query->where('recinto_id', $request->recinto_id);
        }

        // 3. Filtro por Deporte
        if ($request->filled('deporte')) {
            $query->where('deporte', $request->deporte);
        }

        // 4. Filtro por Fecha específica
        if ($request->filled('fecha')) {
            $query->whereDate('fecha_reserva', $request->fecha);
        }

        // Ordenar por fecha descendente y paginar
        $reservas = $query->orderBy('fecha_reserva', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20) // 20 reservas por página
            ->withQueryString(); // Mantener los filtros en la paginación
        
        // Cargar recintos para el filtro
        $recintos = Recinto::orderBy('nombre')->get();
        
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
            'recintos'
        ));
    }
}