<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reserva;
use App\Models\Recinto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EstadisticasController extends Controller
{
    public function index(Request $request)
    {
        // Rango de fechas (últimos 30 días por defecto)
        $fechaInicio = $request->get('fecha_inicio', Carbon::now()->subDays(30)->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', Carbon::now()->format('Y-m-d'));
        
        // Deportes más populares
        $deportesPopulares = Reserva::select('deporte', DB::raw('COUNT(*) as total'))
            ->whereBetween('fecha_reserva', [$fechaInicio, $fechaFin])
            ->groupBy('deporte')
            ->orderBy('total', 'desc')
            ->get();
        
        // Reservas por estado
        $reservasPorEstado = Reserva::select('estado', DB::raw('COUNT(*) as total'))
            ->whereBetween('fecha_reserva', [$fechaInicio, $fechaFin])
            ->groupBy('estado')
            ->get();
        
        // Recintos más solicitados
        $recintosMasSolicitados = Reserva::select('recintos.nombre', DB::raw('COUNT(reservas.id) as total'))
            ->join('recintos', 'reservas.recinto_id', '=', 'recintos.id')
            ->whereBetween('reservas.fecha_reserva', [$fechaInicio, $fechaFin])
            ->groupBy('recintos.nombre', 'recintos.id')
            ->orderBy('total', 'desc')
            ->get();
        
        // Reservas por mes (últimos 6 meses)
        $reservasPorMes = Reserva::select(
                DB::raw('YEAR(fecha_reserva) as año'),
                DB::raw('MONTH(fecha_reserva) as mes'),
                DB::raw('COUNT(*) as total')
            )
            ->where('fecha_reserva', '>=', Carbon::now()->subMonths(6))
            ->groupBy('año', 'mes')
            ->orderBy('año', 'asc')
            ->orderBy('mes', 'asc')
            ->get();
        
        // Organizaciones más activas
        $organizacionesMasActivas = Reserva::select('nombre_organizacion', DB::raw('COUNT(*) as total'))
            ->whereBetween('fecha_reserva', [$fechaInicio, $fechaFin])
            ->groupBy('nombre_organizacion')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();
        
        // Días de la semana más solicitados
        $diasSemanaPopulares = Reserva::select(
                DB::raw('DAYOFWEEK(fecha_reserva) as dia_semana'),
                DB::raw('COUNT(*) as total')
            )
            ->whereBetween('fecha_reserva', [$fechaInicio, $fechaFin])
            ->groupBy('dia_semana')
            ->orderBy('dia_semana', 'asc')
            ->get()
            ->map(function($item) {
                $dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
                $item->dia_nombre = $dias[$item->dia_semana - 1];
                return $item;
            });
        
        // Horarios más solicitados
        $horariosPopulares = Reserva::select(
                DB::raw('HOUR(hora_inicio) as hora'),
                DB::raw('COUNT(*) as total')
            )
            ->whereBetween('fecha_reserva', [$fechaInicio, $fechaFin])
            ->groupBy('hora')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();
        
        // Estadísticas generales
        $totalReservas = Reserva::whereBetween('fecha_reserva', [$fechaInicio, $fechaFin])->count();
        $reservasAprobadas = Reserva::where('estado', 'aprobada')
            ->whereBetween('fecha_reserva', [$fechaInicio, $fechaFin])
            ->count();
        $reservasPendientes = Reserva::where('estado', 'pendiente')
            ->whereBetween('fecha_reserva', [$fechaInicio, $fechaFin])
            ->count();
        $reservasRechazadas = Reserva::where('estado', 'rechazada')
            ->whereBetween('fecha_reserva', [$fechaInicio, $fechaFin])
            ->count();
        
        $tasaAprobacion = $totalReservas > 0 ? round(($reservasAprobadas / $totalReservas) * 100, 1) : 0;
        
        return view('admin.estadisticas.index', compact(
            'deportesPopulares',
            'reservasPorEstado',
            'recintosMasSolicitados',
            'reservasPorMes',
            'organizacionesMasActivas',
            'diasSemanaPopulares',
            'horariosPopulares',
            'totalReservas',
            'reservasAprobadas',
            'reservasPendientes',
            'reservasRechazadas',
            'tasaAprobacion',
            'fechaInicio',
            'fechaFin'
        ));
    }
}