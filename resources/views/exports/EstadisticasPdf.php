<?php

namespace App\Exports;

use App\Models\Reserva;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use DB;

class EstadisticasPdf
{
    protected $fechaInicio;
    protected $fechaFin;

    public function __construct($fechaInicio, $fechaFin)
    {
        $this->fechaInicio = Carbon::createFromFormat('Y-m-d', $fechaInicio)->startOfDay();
        $this->fechaFin = Carbon::createFromFormat('Y-m-d', $fechaFin)->endOfDay();
    }

    public function generate()
    {
        // Recopilar datos
        $data = $this->recopilarDatos();

        // Generar PDF
        $pdf = Pdf::loadView('exports.estadisticas_pdf', $data);

        // Configurar opciones del PDF
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOption('enable_php', true);

        // Descargar PDF
        return $pdf->download('estadisticas_' . now()->format('d-m-Y-His') . '.pdf');
    }

    private function recopilarDatos()
    {
        $totalReservas = Reserva::whereBetween('fecha_reserva', [$this->fechaInicio, $this->fechaFin])->count();
        $reservasAprobadas = Reserva::whereBetween('fecha_reserva', [$this->fechaInicio, $this->fechaFin])
            ->where('estado', 'aprobada')
            ->count();
        $tasaAprobacion = $totalReservas > 0 ? round(($reservasAprobadas / $totalReservas) * 100) : 0;

        $recintosMasSolicitados = Reserva::selectRaw('recintos.nombre, COUNT(reservas.id) as total')
            ->join('recintos', 'reservas.recinto_id', '=', 'recintos.id')
            ->whereBetween('reservas.fecha_reserva', [$this->fechaInicio, $this->fechaFin])
            ->where('reservas.estado', 'aprobada')
            ->groupBy('recintos.id', 'recintos.nombre')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $deportesPopulares = Reserva::selectRaw('deporte, COUNT(*) as total')
            ->whereBetween('fecha_reserva', [$this->fechaInicio, $this->fechaFin])
            ->where('estado', 'aprobada')
            ->groupBy('deporte')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        return [
            'totalReservas' => $totalReservas,
            'reservasAprobadas' => $reservasAprobadas,
            'tasaAprobacion' => $tasaAprobacion,
            'recintosMasSolicitados' => $recintosMasSolicitados,
            'deportesPopulares' => $deportesPopulares,
            'fechaInicio' => $this->fechaInicio->format('d/m/Y'),
            'fechaFin' => $this->fechaFin->format('d/m/Y'),
        ];
    }
}