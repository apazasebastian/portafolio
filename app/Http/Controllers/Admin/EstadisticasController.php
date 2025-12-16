<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reserva;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use Barryvdh\DomPDF\Facade\Pdf;

class EstadisticasController extends Controller
{
    public function aplicarFiltros(Request $request)
    {
        // Convertir a entero si no está vacío / Aun falta agregar controles de filtros, pendiente
        $recintoId = $request->input('recinto_id') ? (int)$request->input('recinto_id') : null;
        
        session([
            'filtro_recinto_id' => $recintoId,
            'filtro_estado' => $request->input('estado'),
        ]);
        
        return back();
    }

    public function limpiarFiltros()
    {
        session()->forget(['filtro_recinto_id', 'filtro_estado']);
        return back();
    }

    public function index(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio') 
            ? Carbon::createFromFormat('Y-m-d', $request->input('fecha_inicio'))->startOfDay()
            : now()->startOfMonth();
            
        $fechaFin = $request->input('fecha_fin')
            ? Carbon::createFromFormat('Y-m-d', $request->input('fecha_fin'))->endOfDay()
            : now()->endOfDay();

        //  COMPARATIVA ENTRE PERIODOS (NUEVO) 
        $periodoComparacion = $request->input('periodo_comparacion');
        $datosComparacion = null;
        
        if ($periodoComparacion) {
            $datosComparacion = $this->obtenerComparativaPeriodos($fechaInicio, $fechaFin, $periodoComparacion);
        }

        $params = [
            'fechaInicio' => $fechaInicio->format('Y-m-d'),
            'fechaFin' => $fechaFin->format('Y-m-d'),
            'nombrePeriodo' => $this->getNombrePeriodo($fechaInicio, $fechaFin),
            'periodoComparacion' => $periodoComparacion,
            'datosComparacion' => $datosComparacion,
        ];

        $totalReservas = $this->getTotalReservas($fechaInicio, $fechaFin);
        $reservasAprobadas = $this->getReservasAprobadas($fechaInicio, $fechaFin);
        $reservasPendientes = $this->getReservasPendientes($fechaInicio, $fechaFin);
        $reservasRechazadas = $this->getReservasRechazadas($fechaInicio, $fechaFin);
        $tasaAprobacion = $totalReservas > 0 ? round(($reservasAprobadas / $totalReservas) * 100) : 0;

        //  TASA DE RECHAZO (NUEVO) 
        $tasaRechazo = $totalReservas > 0 ? round(($reservasRechazadas / $totalReservas) * 100) : 0;

        $params += [
            'totalReservas' => $totalReservas,
            'reservasAprobadas' => $reservasAprobadas,
            'reservasPendientes' => $reservasPendientes,
            'reservasRechazadas' => $reservasRechazadas,
            'tasaAprobacion' => $tasaAprobacion,
            'tasaRechazo' => $tasaRechazo,
        ];

        $params += [
            'deportesPopulares' => $this->getDeportesPopulares($fechaInicio, $fechaFin),
            'recintosMasSolicitados' => $this->getRecintosMasSolicitados($fechaInicio, $fechaFin),
            'diasSemanaPopulares' => $this->getDiasSemanaPopulares($fechaInicio, $fechaFin),
            'horariosPopulares' => $this->getHorariosPopulares($fechaInicio, $fechaFin),
            'organizacionesMasActivas' => $this->getOrganizacionesMasActivas($fechaInicio, $fechaFin),
        ];

        $params += [
            'reservasConUso' => $this->getReservasConUso($fechaInicio, $fechaFin),
            'incidenciasReportadas' => $this->getIncidenciasReportadas($fechaInicio, $fechaFin),
            'danosReportados' => $this->getDanosReportados($fechaInicio, $fechaFin),
        ];

        return view('admin.estadisticas.index', $params);
    }

    //  NUEVO MÉTODO: COMPARATIVA ENTRE PERIODOS 
    private function obtenerComparativaPeriodos($fechaInicio, $fechaFin, $periodoComparacion)
    {
        // Calcular fechas del periodo de comparación
        $diasDiferencia = $fechaInicio->diffInDays($fechaFin);
        
        switch ($periodoComparacion) {
            case 'mes_anterior':
                $fechaInicioComp = $fechaInicio->copy()->subMonth()->startOfMonth();
                $fechaFinComp = $fechaInicio->copy()->subMonth()->endOfMonth();
                break;
            case 'trimestre_anterior':
                $fechaInicioComp = $fechaInicio->copy()->subMonths(3);
                $fechaFinComp = $fechaFin->copy()->subMonths(3);
                break;
            case 'año_anterior':
                $fechaInicioComp = $fechaInicio->copy()->subYear();
                $fechaFinComp = $fechaFin->copy()->subYear();
                break;
            default:
                // Periodo personalizado (mismo rango de días hacia atrás)
                $fechaInicioComp = $fechaInicio->copy()->subDays($diasDiferencia + 1);
                $fechaFinComp = $fechaInicio->copy()->subDay();
        }

        // Obtener datos del periodo actual
        $actual = [
            'total' => $this->getTotalReservas($fechaInicio, $fechaFin),
            'aprobadas' => $this->getReservasAprobadas($fechaInicio, $fechaFin),
            'rechazadas' => $this->getReservasRechazadas($fechaInicio, $fechaFin),
            'pendientes' => $this->getReservasPendientes($fechaInicio, $fechaFin),
        ];

        // Obtener datos del periodo de comparación
        $comparacion = [
            'total' => $this->getTotalReservas($fechaInicioComp, $fechaFinComp),
            'aprobadas' => $this->getReservasAprobadas($fechaInicioComp, $fechaFinComp),
            'rechazadas' => $this->getReservasRechazadas($fechaInicioComp, $fechaFinComp),
            'pendientes' => $this->getReservasPendientes($fechaInicioComp, $fechaFinComp),
        ];

        // Calcular diferencias y porcentajes
        $diferencias = [
            'total' => $actual['total'] - $comparacion['total'],
            'aprobadas' => $actual['aprobadas'] - $comparacion['aprobadas'],
            'rechazadas' => $actual['rechazadas'] - $comparacion['rechazadas'],
            'pendientes' => $actual['pendientes'] - $comparacion['pendientes'],
        ];

        $porcentajes = [
            'total' => $comparacion['total'] > 0 ? round((($actual['total'] - $comparacion['total']) / $comparacion['total']) * 100, 1) : 0,
            'aprobadas' => $comparacion['aprobadas'] > 0 ? round((($actual['aprobadas'] - $comparacion['aprobadas']) / $comparacion['aprobadas']) * 100, 1) : 0,
            'rechazadas' => $comparacion['rechazadas'] > 0 ? round((($actual['rechazadas'] - $comparacion['rechazadas']) / $comparacion['rechazadas']) * 100, 1) : 0,
            'pendientes' => $comparacion['pendientes'] > 0 ? round((($actual['pendientes'] - $comparacion['pendientes']) / $comparacion['pendientes']) * 100, 1) : 0,
        ];

        return [
            'periodo_actual' => [
                'nombre' => $this->getNombrePeriodo($fechaInicio, $fechaFin),
                'datos' => $actual,
            ],
            'periodo_comparacion' => [
                'nombre' => $this->getNombrePeriodo($fechaInicioComp, $fechaFinComp),
                'datos' => $comparacion,
            ],
            'diferencias' => $diferencias,
            'porcentajes' => $porcentajes,
        ];
    }

    private function applyFilters($query)
    {
        if (session('filtro_recinto_id')) {
            $query->where('reservas.recinto_id', session('filtro_recinto_id'));
        }
        
        if (session('filtro_estado')) {
            $query->where('reservas.estado', session('filtro_estado'));
        }
        
        return $query;
    }

    private function getNombrePeriodo($inicio, $fin)
    {
        $inicio->setLocale('es');
        $fin->setLocale('es');
        
        $mesInicio = $inicio->isoFormat('MMMM');
        $mesFin = $fin->isoFormat('MMMM');
        $año = $inicio->year;
        
        if ($inicio->month === $fin->month && $inicio->year === $fin->year) {
            return ucfirst($mesInicio) . ' ' . $año;
        }
        
        if ($inicio->year === $fin->year) {
            return ucfirst($mesInicio) . ' - ' . ucfirst($mesFin) . ' ' . $año;
        }
        
        return ucfirst($mesInicio) . ' ' . $inicio->year . ' - ' . ucfirst($mesFin) . ' ' . $fin->year;
    }

    private function getTotalReservas($inicio, $fin)
    {
        $query = Reserva::whereBetween('fecha_reserva', [$inicio, $fin]);
        return $this->applyFilters($query)->count();
    }

    private function getReservasAprobadas($inicio, $fin)
    {
        $query = Reserva::whereBetween('fecha_reserva', [$inicio, $fin])->where('estado', 'aprobada');
        return $this->applyFilters($query)->count();
    }

    private function getReservasPendientes($inicio, $fin)
    {
        $query = Reserva::whereBetween('fecha_reserva', [$inicio, $fin])->where('estado', 'pendiente');
        return $this->applyFilters($query)->count();
    }

    private function getReservasRechazadas($inicio, $fin)
    {
        $query = Reserva::whereBetween('fecha_reserva', [$inicio, $fin])->where('estado', 'rechazada');
        return $this->applyFilters($query)->count();
    }

    private function getDeportesPopulares($inicio, $fin)
    {
        $query = Reserva::selectRaw('deporte, COUNT(*) as total')
            ->whereBetween('fecha_reserva', [$inicio, $fin])
            ->where('estado', 'aprobada');
        return $this->applyFilters($query)->groupBy('deporte')->orderByDesc('total')->limit(8)->get();
    }

    private function getRecintosMasSolicitados($inicio, $fin)
    {
        $query = Reserva::selectRaw('recintos.nombre, recintos.id, COUNT(reservas.id) as total')
            ->join('recintos', 'reservas.recinto_id', '=', 'recintos.id')
            ->whereBetween('reservas.fecha_reserva', [$inicio, $fin])
            ->where('reservas.estado', 'aprobada');
        return $this->applyFilters($query)->groupBy('recintos.id', 'recintos.nombre')->orderByDesc('total')->limit(5)->get();
    }

    private function getDiasSemanaPopulares($inicio, $fin)
    {
        $dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        
        $query = Reserva::selectRaw('DAYNAME(fecha_reserva) as dia_nombre, DAYOFWEEK(fecha_reserva) as dia_numero, COUNT(*) as total')
            ->whereBetween('fecha_reserva', [$inicio, $fin])
            ->where('estado', 'aprobada');
        
        $result = $this->applyFilters($query)->groupBy('dia_numero', 'dia_nombre')->orderBy('dia_numero')->get();

        return $result->map(function ($item) use ($dias) {
            $item->dia_nombre = $dias[$item->dia_numero - 1] ?? 'Desconocido';
            return $item;
        });
    }

    private function getHorariosPopulares($inicio, $fin)
    {
        $query = Reserva::selectRaw('HOUR(hora_inicio) as hora, COUNT(*) as total')
            ->whereBetween('fecha_reserva', [$inicio, $fin])
            ->where('estado', 'aprobada');
        return $this->applyFilters($query)->groupBy('hora')->orderByDesc('total')->limit(10)->get();
    }

    private function getOrganizacionesMasActivas($inicio, $fin)
    {
        $query = Reserva::selectRaw('nombre_organizacion, COUNT(*) as total')
            ->whereBetween('fecha_reserva', [$inicio, $fin])
            ->where('estado', 'aprobada')
            ->whereNotNull('nombre_organizacion')
            ->where('nombre_organizacion', '!=', '');
        
        return $this->applyFilters($query)->groupBy('nombre_organizacion')->orderByDesc('total')->limit(10)->get()
            ->map(function ($item) {
                $item->nombre_organizacion = $item->nombre_organizacion ?? 'Sin especificar';
                return $item;
            });
    }

    private function getReservasConUso($inicio, $fin)
    {
        $query = Reserva::whereBetween('fecha_reserva', [$inicio, $fin])->where('estado', 'aprobada');
        return $this->applyFilters($query)->count();
    }

    private function getIncidenciasReportadas($inicio, $fin)
    {
        return DB::table('incidencias')->whereBetween('created_at', [$inicio, $fin])->count();
    }

    private function getDanosReportados($inicio, $fin)
    {
        return DB::table('incidencias')->whereBetween('created_at', [$inicio, $fin])->where('tipo', 'dano')->count();
    }

    public function exportarExcel(Request $request)
    {
        try {
            $fechaInicio = $request->input('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
            $fechaFin = $request->input('fecha_fin', now()->format('Y-m-d'));

            $query = Reserva::whereBetween('fecha_reserva', [$fechaInicio, $fechaFin])
                ->with('recinto')
                ->orderBy('fecha_reserva', 'desc');
            
            if (session('filtro_recinto_id')) {
                $query->where('recinto_id', session('filtro_recinto_id'));
            }
            if (session('filtro_estado')) {
                $query->where('estado', session('filtro_estado'));
            }
            
            $reservas = $query->get();

            $csvFileName = 'estadisticas_' . Carbon::now()->format('d-m-Y-His') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="' . $csvFileName . '"',
            ];

            //  REGISTRAR EN AUDITORÍA ANTES DE RETORNAR 
            AuditLog::log(
                'exportar_excel',
                "Exportación de estadísticas a Excel (período: {$fechaInicio} a {$fechaFin}, {$reservas->count()} registros)"
            );

            $callback = function () use ($reservas) {
                $file = fopen('php://output', 'w');
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                
                fputcsv($file, ['ID', 'Recinto', 'Deporte', 'Fecha', 'Hora Inicio', 'Hora Fin', 'Organización', 'Estado', 'Participantes'], ',');

                foreach ($reservas as $reserva) {
                    fputcsv($file, [
                        $reserva->id,
                        $reserva->recinto->nombre ?? 'N/A',
                        $reserva->deporte,
                        $reserva->fecha_reserva->format('d/m/Y'),
                        $reserva->hora_inicio->format('H:i'),
                        $reserva->hora_fin->format('H:i'),
                        $reserva->nombre_organizacion ?? 'N/A',
                        ucfirst($reserva->estado),
                        $reserva->cantidad_personas ?? 0
                    ], ',');
                }
                
                fclose($file);
            };

            return response()->streamDownload($callback, $csvFileName, $headers);

        } catch (\Exception $e) {
            return back()->withErrors('Error al descargar CSV: ' . $e->getMessage());
        }
    }

    public function exportarPdf(Request $request)
    {
        try {
            $fechaInicio = $request->input('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
            $fechaFin = $request->input('fecha_fin', now()->format('Y-m-d'));

            $query = Reserva::whereBetween('fecha_reserva', [$fechaInicio, $fechaFin])
                ->with('recinto')
                ->orderBy('fecha_reserva', 'desc');
            
            if (session('filtro_recinto_id')) {
                $query->where('recinto_id', session('filtro_recinto_id'));
            }
            if (session('filtro_estado')) {
                $query->where('estado', session('filtro_estado'));
            }
            
            $reservas = $query->get();

            $totalReservas = $reservas->count();
            $reservasAprobadas = $reservas->where('estado', 'aprobada')->count();
            $tasaAprobacion = $totalReservas > 0 ? round(($reservasAprobadas / $totalReservas) * 100) : 0;

            $desde = Carbon::parse($fechaInicio)->locale('es')->isoFormat('D [de] MMMM [de] YYYY');
            $hasta = Carbon::parse($fechaFin)->locale('es')->isoFormat('D [de] MMMM [de] YYYY');

            // ⚠️ REGISTRAR EN AUDITORÍA ANTES DE RETORNAR ⚠️
            AuditLog::log(
                'exportar_pdf',
                "Exportación de estadísticas a PDF (período: {$fechaInicio} a {$fechaFin}, {$totalReservas} registros)"
            );

            $pdf = Pdf::loadView('admin.estadisticas.pdf', compact(
                'reservas',
                'totalReservas',
                'reservasAprobadas',
                'tasaAprobacion',
                'desde',
                'hasta'
            ));

            $filename = 'estadisticas_' . Carbon::now()->format('d-m-Y-His') . '.pdf';

            return $pdf->download($filename);

        } catch (\Exception $e) {
            return back()->withErrors('Error al descargar PDF: ' . $e->getMessage());
        }
    }
}