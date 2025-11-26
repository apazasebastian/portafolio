<?php

namespace App\Exports;

use App\Models\Reserva;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class EstadisticasExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $fechaInicio;
    protected $fechaFin;

    public function __construct($fechaInicio, $fechaFin)
    {
        $this->fechaInicio = Carbon::createFromFormat('Y-m-d', $fechaInicio)->startOfDay();
        $this->fechaFin = Carbon::createFromFormat('Y-m-d', $fechaFin)->endOfDay();
    }

    public function collection()
    {
        return Reserva::with('recinto', 'organizacion')
            ->whereBetween('fecha_reserva', [$this->fechaInicio, $this->fechaFin])
            ->orderByDesc('fecha_reserva')
            ->get()
            ->map(function ($reserva, $index) {
                return [
                    $index + 1,
                    $reserva->id,
                    $reserva->recinto->nombre ?? 'N/A',
                    $reserva->organizacion->nombre ?? 'N/A',
                    $reserva->deporte ?? 'No especificado',
                    $reserva->fecha_reserva->format('d/m/Y'),
                    $reserva->hora_inicio->format('H:i'),
                    $reserva->hora_fin->format('H:i'),
                    ucfirst($reserva->estado),
                    $reserva->cantidad_participantes ?? 0,
                    $reserva->observaciones ?? '',
                ];
            });
    }

    public function headings(): array
    {
        return [
            '#',
            'ID',
            'Recinto',
            'Organización',
            'Deporte',
            'Fecha',
            'Hora Inicio',
            'Hora Fin',
            'Estado',
            'Participantes',
            'Observaciones'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Estilos para el encabezado
        $sheet->getStyle('A1:K1')->getFont()->setBold(true);
        $sheet->getStyle('A1:K1')->getFont()->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle('A1:K1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('FF4472C4');

        // Centrar encabezado
        $sheet->getStyle('A1:K1')->getAlignment()
            ->setHorizontal('center')
            ->setVertical('center');

        return [];
    }
}