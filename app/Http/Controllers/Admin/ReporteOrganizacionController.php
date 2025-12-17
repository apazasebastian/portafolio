<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reserva;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteOrganizacionController extends Controller
{
    /**
     * Mostrar vista principal del reporte
     */
    public function index()
    {
        return view('admin.reportes.organizacion');
    }

    /**
     * Buscar organizaciones para autocompletado
     */
    public function buscarOrganizaciones(Request $request)
    {
        $termino = $request->get('termino', '');
        
        $organizaciones = Reserva::select('nombre_organizacion', 'representante_nombre', 'email', 'rut')
            ->whereNotNull('nombre_organizacion')
            ->where('nombre_organizacion', '!=', '')
            ->where(function($query) use ($termino) {
                $query->where('nombre_organizacion', 'LIKE', "%{$termino}%")
                      ->orWhere('rut', 'LIKE', "%{$termino}%");
            })
            ->groupBy('nombre_organizacion', 'representante_nombre', 'email', 'rut')
            ->limit(10)
            ->get();

        return response()->json($organizaciones);
    }

    /**
     * Generar reporte completo de una organización
     */
    public function generarReporte(Request $request)
    {
        $request->validate([
            'organizacion' => 'required|string',
        ]);

        $nombreOrganizacion = $request->organizacion;

        // Obtener todas las reservas de la organización
        $reservas = Reserva::with(['recinto'])
            ->where('nombre_organizacion', $nombreOrganizacion)
            ->orderBy('fecha_reserva', 'desc')
            ->get();

        if ($reservas->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontraron reservas para esta organización'
            ], 404);
        }

        // Información de la organización (tomar del registro más reciente)
        $infoOrganizacion = [
            'nombre' => $nombreOrganizacion,
            'representante' => $reservas->first()->representante_nombre,
            'email' => $reservas->first()->email,
            'telefono' => $reservas->first()->telefono,
            'rut' => $reservas->first()->rut,
        ];

        // Estadísticas generales
        $estadisticas = [
            'total' => $reservas->count(),
            'aprobadas' => $reservas->where('estado', 'aprobada')->count(),
            'pendientes' => $reservas->where('estado', 'pendiente')->count(),
            'rechazadas' => $reservas->where('estado', 'rechazada')->count(),
            'canceladas' => $reservas->whereNotNull('fecha_cancelacion')->count(),
        ];

        // Calcular porcentajes
        $estadisticas['pct_aprobadas'] = $this->calcularPorcentaje($estadisticas['aprobadas'], $estadisticas['total']);
        $estadisticas['pct_pendientes'] = $this->calcularPorcentaje($estadisticas['pendientes'], $estadisticas['total']);
        $estadisticas['pct_rechazadas'] = $this->calcularPorcentaje($estadisticas['rechazadas'], $estadisticas['total']);

        // Calcular horas totales y promedio
        $horasTotales = 0;
        foreach ($reservas as $reserva) {
            $inicio = \Carbon\Carbon::parse($reserva->hora_inicio);
            $fin = \Carbon\Carbon::parse($reserva->hora_fin);
            $horasTotales += $inicio->diffInHours($fin);
        }
        
        $estadisticas['horas_totales'] = $horasTotales;
        $estadisticas['horas_promedio'] = round($horasTotales / $estadisticas['total'], 1);

        // Deportes más solicitados
        $deportes = $reservas->groupBy('deporte')
            ->map(function ($grupo) {
                return $grupo->count();
            })
            ->sortDesc()
            ->take(5);

        // Recintos más utilizados
        $recintos = $reservas->groupBy('recinto.nombre')
            ->map(function ($grupo) use ($reservas) {
                return [
                    'cantidad' => $grupo->count(),
                    'porcentaje' => $this->calcularPorcentaje($grupo->count(), $reservas->count())
                ];
            })
            ->sortByDesc('cantidad')
            ->take(5);

        // Reservas por mes (últimos 12 meses)
        $reservasPorMes = $reservas
            ->filter(function($reserva) {
                return $reserva->fecha_reserva->isAfter(now()->subMonths(12));
            })
            ->groupBy(function($reserva) {
                return $reserva->fecha_reserva->format('Y-m');
            })
            ->map(function ($grupo) {
                return $grupo->count();
            })
            ->sortKeys();

        // Detalles de todas las reservas
        $reservasDetalle = $reservas->map(function($reserva) {
            return [
                'id' => $reserva->id,
                'fecha' => $reserva->fecha_reserva->format('Y-m-d'),
                'fecha_formato' => $reserva->fecha_reserva->format('d/m/Y'),
                'hora_inicio' => \Carbon\Carbon::parse($reserva->hora_inicio)->format('H:i'),
                'hora_fin' => \Carbon\Carbon::parse($reserva->hora_fin)->format('H:i'),
                'recinto' => $reserva->recinto->nombre ?? 'N/A',
                'deporte' => $reserva->deporte,
                'personas' => $reserva->cantidad_personas,
                'estado' => $reserva->estado,
                'motivo_rechazo' => $reserva->motivo_rechazo,
                'observaciones' => $reserva->observaciones,
            ];
        });

        // Análisis de patrones
        $analisis = [
            'dia_semana_favorito' => $this->obtenerDiaFavorito($reservas),
            'horario_favorito' => $this->obtenerHorarioFavorito($reservas),
            'deporte_favorito' => $deportes->keys()->first(),
            'recinto_favorito' => $recintos->keys()->first(),
            'tasa_aprobacion' => $this->calcularPorcentaje($estadisticas['aprobadas'], $estadisticas['total'] - $estadisticas['pendientes']),
        ];

        return response()->json([
            'success' => true,
            'organizacion' => $infoOrganizacion,
            'estadisticas' => $estadisticas,
            'deportes' => $deportes,
            'recintos' => $recintos,
            'reservas_por_mes' => $reservasPorMes,
            'reservas' => $reservasDetalle,
            'analisis' => $analisis,
        ]);
    }

    /**
     * Exportar reporte a PDF
     */
    public function exportarPDF(Request $request)
    {
        $request->validate([
            'organizacion' => 'required|string',
        ]);

        $nombreOrganizacion = $request->organizacion;

        // Obtener todas las reservas de la organización
        $reservas = Reserva::with(['recinto'])
            ->where('nombre_organizacion', $nombreOrganizacion)
            ->orderBy('fecha_reserva', 'desc')
            ->get();

        if ($reservas->isEmpty()) {
            return back()->with('error', 'No se encontraron reservas para esta organización');
        }

        // Información de la organización
        $infoOrganizacion = [
            'nombre' => $nombreOrganizacion,
            'representante' => $reservas->first()->representante_nombre,
            'email' => $reservas->first()->email,
            'telefono' => $reservas->first()->telefono,
            'rut' => $reservas->first()->rut,
        ];

        // Estadísticas generales
        $estadisticas = [
            'total' => $reservas->count(),
            'aprobadas' => $reservas->where('estado', 'aprobada')->count(),
            'pendientes' => $reservas->where('estado', 'pendiente')->count(),
            'rechazadas' => $reservas->where('estado', 'rechazada')->count(),
            'canceladas' => $reservas->whereNotNull('fecha_cancelacion')->count(),
        ];

        $estadisticas['pct_aprobadas'] = $this->calcularPorcentaje($estadisticas['aprobadas'], $estadisticas['total']);
        $estadisticas['pct_pendientes'] = $this->calcularPorcentaje($estadisticas['pendientes'], $estadisticas['total']);
        $estadisticas['pct_rechazadas'] = $this->calcularPorcentaje($estadisticas['rechazadas'], $estadisticas['total']);

        // Calcular horas totales
        $horasTotales = 0;
        foreach ($reservas as $reserva) {
            $inicio = \Carbon\Carbon::parse($reserva->hora_inicio);
            $fin = \Carbon\Carbon::parse($reserva->hora_fin);
            $horasTotales += $inicio->diffInHours($fin);
        }
        
        $estadisticas['horas_totales'] = $horasTotales;
        $estadisticas['horas_promedio'] = round($horasTotales / $estadisticas['total'], 1);

        // Deportes más solicitados
        $deportesData = $reservas->groupBy('deporte')
            ->map(function ($grupo) {
                return $grupo->count();
            })
            ->sortDesc()
            ->take(5);

        $deportes = [];
        $index = 0;
        foreach ($deportesData as $nombre => $cantidad) {
            $deportes[] = [
                'nombre' => $nombre,
                'cantidad' => $cantidad
            ];
            $index++;
        }

        // Recintos más utilizados
        $recintosData = $reservas->groupBy('recinto.nombre')
            ->map(function ($grupo) use ($reservas) {
                return [
                    'cantidad' => $grupo->count(),
                    'porcentaje' => $this->calcularPorcentaje($grupo->count(), $reservas->count())
                ];
            })
            ->sortByDesc('cantidad')
            ->take(5);

        $recintos_top = [];
        foreach ($recintosData as $nombre => $datos) {
            $recintos_top[] = [
                'nombre' => $nombre,
                'cantidad' => $datos['cantidad'],
                'porcentaje' => $datos['porcentaje']
            ];
        }

        // Detalles de reservas
        $reservasDetalle = $reservas->map(function($reserva) {
            return [
                'id' => $reserva->id,
                'fecha' => $reserva->fecha_reserva->format('Y-m-d'),
                'fecha_formato' => $reserva->fecha_reserva->format('d/m/Y'),
                'hora_inicio' => \Carbon\Carbon::parse($reserva->hora_inicio)->format('H:i'),
                'hora_fin' => \Carbon\Carbon::parse($reserva->hora_fin)->format('H:i'),
                'recinto' => $reserva->recinto->nombre ?? 'N/A',
                'deporte' => $reserva->deporte,
                'personas' => $reserva->cantidad_personas,
                'estado' => $reserva->estado,
            ];
        })->toArray();

        // Análisis de patrones
        $analisis = [
            'dia_semana_favorito' => $this->obtenerDiaFavorito($reservas),
            'horario_favorito' => $this->obtenerHorarioFavorito($reservas),
            'deporte_favorito' => $deportes[0]['nombre'] ?? 'N/A',
            'recinto_favorito' => $recintos_top[0]['nombre'] ?? 'N/A',
            'tasa_aprobacion' => $this->calcularPorcentaje($estadisticas['aprobadas'], $estadisticas['total'] - $estadisticas['pendientes']),
        ];

        // Generar PDF
        $pdf = \PDF::loadView('admin.reportes.organizacion-pdf', [
            'organizacion' => $infoOrganizacion,
            'estadisticas' => $estadisticas,
            'deportes' => $deportes,
            'recintos_top' => $recintos_top,
            'reservas' => $reservasDetalle,
            'analisis' => $analisis,
        ]);

        // Configurar opciones del PDF
        $pdf->setPaper('letter', 'portrait');
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);

        // Nombre del archivo
        $nombreArchivo = 'reporte_' . \Str::slug($nombreOrganizacion) . '_' . now()->format('Y-m-d') . '.pdf';

        // Descargar PDF
        return $pdf->download($nombreArchivo);
    }

    /**
     * Calcular porcentaje
     */
    private function calcularPorcentaje($parte, $total)
    {
        return $total > 0 ? round(($parte / $total) * 100, 1) : 0;
    }

    /**
     * Obtener día de la semana favorito
     */
    private function obtenerDiaFavorito($reservas)
    {
        $dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        
        $conteo = $reservas->groupBy(function($reserva) {
            return $reserva->fecha_reserva->dayOfWeek;
        })->map(function($grupo) {
            return $grupo->count();
        })->sortDesc();

        $diaFavorito = $conteo->keys()->first();
        
        return $diaFavorito !== null ? $dias[$diaFavorito] : 'N/A';
    }

    /**
     * Obtener horario favorito
     */
    private function obtenerHorarioFavorito($reservas)
    {
        $horarios = [
            'Mañana (06:00-12:00)' => 0,
            'Tarde (12:00-18:00)' => 0,
            'Noche (18:00-23:00)' => 0,
        ];

        foreach ($reservas as $reserva) {
            $hora = (int) \Carbon\Carbon::parse($reserva->hora_inicio)->format('H');
            
            if ($hora >= 6 && $hora < 12) {
                $horarios['Mañana (06:00-12:00)']++;
            } elseif ($hora >= 12 && $hora < 18) {
                $horarios['Tarde (12:00-18:00)']++;
            } else {
                $horarios['Noche (18:00-23:00)']++;
            }
        }

        return array_keys($horarios, max($horarios))[0];
    }
}