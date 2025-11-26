<!-- resources/views/exports/estadisticas_pdf.blade.php -->

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #4472C4;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            color: #4472C4;
            font-size: 28px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        .section-title {
            background-color: #4472C4;
            color: white;
            padding: 10px 15px;
            margin: 15px 0 10px 0;
            font-size: 16px;
            font-weight: bold;
            border-radius: 3px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table thead {
            background-color: #E7E6E6;
        }
        table th {
            padding: 12px;
            text-align: left;
            font-weight: bold;
            border-bottom: 2px solid #4472C4;
        }
        table td {
            padding: 10px 12px;
            border-bottom: 1px solid #ddd;
        }
        table tbody tr:nth-child(even) {
            background-color: #F9F9F9;
        }
        .stats-container {
            display: flex;
            gap: 15px;
            margin: 15px 0;
        }
        .stat-box {
            flex: 1;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            background-color: #F9F9F9;
        }
        .stat-value {
            font-size: 28px;
            font-weight: bold;
            color: #4472C4;
            margin: 10px 0;
        }
        .stat-label {
            font-size: 12px;
            color: #666;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 11px;
            color: #999;
        }
        .date-range {
            background-color: #E7E6E6;
            padding: 10px 15px;
            border-radius: 3px;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📊 Reporte de Estadísticas</h1>
        <p>Sistema de Reserva de Recintos Deportivos - Municipalidad de Arica</p>
    </div>

    <div class="date-range">
        <strong>Período:</strong> {{ $fechaInicio }} al {{ $fechaFin }}
    </div>

    <!-- ESTADÍSTICAS GENERALES -->
    <div class="section">
        <div class="section-title">Estadísticas Generales</div>
        <div class="stats-container">
            <div class="stat-box">
                <div class="stat-label">Total de Reservas</div>
                <div class="stat-value">{{ $totalReservas }}</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Reservas Aprobadas</div>
                <div class="stat-value">{{ $reservasAprobadas }}</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Tasa de Aprobación</div>
                <div class="stat-value">{{ $tasaAprobacion }}%</div>
            </div>
        </div>
    </div>

    <!-- DEPORTES MÁS POPULARES -->
    <div class="section">
        <div class="section-title">Deportes más Populares</div>
        <table>
            <thead>
                <tr>
                    <th>Deporte</th>
                    <th style="text-align: right;">Total de Reservas</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deportesPopulares as $deporte)
                    <tr>
                        <td>{{ $deporte->deporte ?? 'No especificado' }}</td>
                        <td style="text-align: right;">{{ $deporte->total }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" style="text-align: center; color: #999;">No hay datos disponibles</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- RECINTOS MÁS SOLICITADOS -->
    <div class="section">
        <div class="section-title">Recintos más Solicitados</div>
        <table>
            <thead>
                <tr>
                    <th>Recinto</th>
                    <th style="text-align: right;">Total de Reservas</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recintosMasSolicitados as $recinto)
                    <tr>
                        <td>{{ $recinto->nombre }}</td>
                        <td style="text-align: right;">{{ $recinto->total }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" style="text-align: center; color: #999;">No hay datos disponibles</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>Reporte generado el {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>Sistema de Reserva de Recintos Deportivos - Municipalidad de Arica</p>
    </div>
</body>
</html>