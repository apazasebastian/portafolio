<!-- resources/views/exports/estadisticas_pdf.blade.php -->

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 0;
        }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            font-size: 12px;
        }
        .page-wrapper {
            padding: 0;
        }
        
        /* Header institucional */
        .header {
            background-color: #ffffff;
            padding: 20px 30px;
            text-align: center;
            border-bottom: 4px solid #1a1a8e;
        }
        .header img {
            max-height: 50px;
            width: auto;
        }
        .header-title {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #e5e5e5;
        }
        .header-title h1 {
            margin: 0;
            color: #1a1a8e;
            font-size: 22px;
            font-weight: 600;
            letter-spacing: 1px;
        }
        .header-title p {
            margin: 5px 0 0;
            color: #666;
            font-size: 12px;
        }
        
        /* Contenido */
        .content {
            padding: 25px 30px;
        }
        
        .date-range {
            background-color: #1a1a8e;
            color: #ffffff;
            padding: 12px 20px;
            text-align: center;
            margin-bottom: 25px;
            font-size: 13px;
        }
        
        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        .section-title {
            background-color: #1a1a8e;
            color: white;
            padding: 10px 15px;
            margin: 0 0 15px 0;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        /* Estadísticas en cajas */
        .stats-row {
            width: 100%;
            margin-bottom: 20px;
        }
        .stats-row table {
            width: 100%;
            border-collapse: collapse;
        }
        .stat-box {
            width: 33.33%;
            text-align: center;
            padding: 15px;
            background-color: #f8f9fa;
            border: 1px solid #e5e5e5;
        }
        .stat-value {
            font-size: 28px;
            font-weight: bold;
            color: #1a1a8e;
            margin: 8px 0;
        }
        .stat-label {
            font-size: 11px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        /* Tablas de datos */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table.data-table thead {
            background-color: #f0f4f8;
        }
        table.data-table th {
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
            color: #1a1a8e;
            border-bottom: 2px solid #1a1a8e;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        table.data-table td {
            padding: 10px 15px;
            border-bottom: 1px solid #e5e5e5;
            font-size: 12px;
        }
        table.data-table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }
        table.data-table .text-right {
            text-align: right;
        }
        table.data-table .empty-row {
            text-align: center;
            color: #999;
            font-style: italic;
        }
        
        /* Footer institucional */
        .footer {
            background-color: #1a1a8e;
            padding: 15px 30px;
            text-align: center;
            color: #ffffff;
            font-size: 10px;
            position: fixed;
            bottom: 4px;
            left: 0;
            right: 0;
        }
        .footer p {
            margin: 3px 0;
        }
        .footer .light {
            color: rgba(255,255,255,0.7);
        }
        
        /* Barra de colores */
        .color-bar {
            height: 4px;
            background: linear-gradient(to right, #00a651 25%, #f7941d 25%, #f7941d 50%, #00aeef 50%, #00aeef 75%, #ed1c24 75%);
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
        }
    </style>
</head>
<body>
    <div class="page-wrapper">
        <!-- Header con logo institucional -->
        <div class="header">
            <img src="{{ public_path('images/logo-municipalidad.png') }}" alt="Municipalidad de Arica">
            <div class="header-title">
                <h1>REPORTE DE ESTADÍSTICAS</h1>
                <p>Sistema de Reserva de Recintos Deportivos</p>
            </div>
        </div>

        <div class="content">
            <!-- Período del reporte -->
            <div class="date-range">
                <strong>Período:</strong> {{ $fechaInicio }} al {{ $fechaFin }}
            </div>

            <!-- ESTADÍSTICAS GENERALES -->
            <div class="section">
                <div class="section-title">Estadísticas Generales</div>
                <div class="stats-row">
                    <table>
                        <tr>
                            <td class="stat-box">
                                <div class="stat-label">Total de Reservas</div>
                                <div class="stat-value">{{ $totalReservas }}</div>
                            </td>
                            <td class="stat-box">
                                <div class="stat-label">Reservas Aprobadas</div>
                                <div class="stat-value">{{ $reservasAprobadas }}</div>
                            </td>
                            <td class="stat-box">
                                <div class="stat-label">Tasa de Aprobación</div>
                                <div class="stat-value">{{ $tasaAprobacion }}%</div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- DEPORTES MÁS POPULARES -->
            <div class="section">
                <div class="section-title">Deportes más Populares</div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Deporte</th>
                            <th class="text-right">Total de Reservas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($deportesPopulares as $deporte)
                            <tr>
                                <td>{{ $deporte->deporte ?? 'No especificado' }}</td>
                                <td class="text-right">{{ $deporte->total }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="empty-row">No hay datos disponibles</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- RECINTOS MÁS SOLICITADOS -->
            <div class="section">
                <div class="section-title">Recintos más Solicitados</div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Recinto</th>
                            <th class="text-right">Total de Reservas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recintosMasSolicitados as $recinto)
                            <tr>
                                <td>{{ $recinto->nombre }}</td>
                                <td class="text-right">{{ $recinto->total }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="empty-row">No hay datos disponibles</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer institucional -->
        <div class="footer">
            <p>Reporte generado el {{ now()->format('d/m/Y H:i:s') }}</p>
            <p class="light">© {{ date('Y') }} Municipalidad de Arica - Oficina de Deportes</p>
        </div>
        
        <!-- Barra de colores institucional -->
        <div class="color-bar"></div>
    </div>
</body>
</html>