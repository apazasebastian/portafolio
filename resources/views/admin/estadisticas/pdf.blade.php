<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Estadísticas</title>
    <style>
        @page {
            margin: 0;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            color: #333;
            line-height: 1.5;
            font-size: 12px;
        }
        
        /* Header institucional con logo */
        .header-logo {
            background-color: #ffffff;
            padding: 20px 30px;
            text-align: center;
            border-bottom: 4px solid #1a1a8e;
        }
        .header-logo img {
            max-height: 50px;
            width: auto;
        }
        
        /* Título del reporte */
        .header-title {
            background-color: #1a1a8e;
            color: white;
            padding: 20px 30px;
            text-align: center;
        }
        .header-title h1 {
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 5px;
            letter-spacing: 1px;
        }
        .header-title p {
            font-size: 13px;
            opacity: 0.9;
        }
        .header-title .periodo {
            margin-top: 10px;
            font-size: 14px;
        }
        .header-title .fecha {
            font-weight: bold;
            color: #ffffff;
        }
        
        /* Contenido principal */
        .content {
            padding: 25px 30px;
        }
        
        /* Información general */
        .info-section {
            background-color: #f0f4f8;
            border-left: 4px solid #1a1a8e;
            padding: 15px 20px;
            margin-bottom: 25px;
        }
        .info-section p {
            margin-bottom: 6px;
            font-size: 13px;
        }
        .info-section strong {
            color: #1a1a8e;
        }
        
        /* Estadísticas en cajas */
        .stats-container {
            width: 100%;
            margin-bottom: 25px;
        }
        .stats-container table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 10px 0;
        }
        .stat-box {
            width: 33.33%;
            text-align: center;
            padding: 20px 15px;
            background-color: #1a1a8e;
            color: white;
        }
        .stat-box.aprobadas {
            background-color: #059669;
        }
        .stat-box.tasa {
            background-color: #0284c7;
        }
        .stat-box h3 {
            font-size: 11px;
            font-weight: 400;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            opacity: 0.9;
            margin-bottom: 8px;
        }
        .stat-box .number {
            font-size: 28px;
            font-weight: bold;
        }
        .stat-box .percentage {
            font-size: 12px;
            margin-top: 5px;
            opacity: 0.85;
        }
        
        /* Secciones de tablas */
        .table-section {
            margin-top: 25px;
            page-break-inside: avoid;
        }
        .table-section h2 {
            background-color: #1a1a8e;
            color: white;
            padding: 10px 15px;
            margin-bottom: 0;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 11px;
        }
        table.data-table thead {
            background-color: #f0f4f8;
        }
        table.data-table th {
            padding: 10px 12px;
            text-align: left;
            font-weight: 600;
            color: #1a1a8e;
            border: 1px solid #e5e5e5;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        table.data-table td {
            padding: 8px 12px;
            border: 1px solid #e5e5e5;
            background-color: white;
        }
        table.data-table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }
        table.data-table tbody tr:nth-child(even) td {
            background-color: #f9fafb;
        }
        
        /* Footer institucional */
        .footer {
            background-color: #1a1a8e;
            padding: 15px 30px;
            text-align: center;
            color: #ffffff;
            font-size: 10px;
            margin-top: 30px;
        }
        .footer p {
            margin-bottom: 3px;
        }
        .footer .light {
            color: rgba(255,255,255,0.7);
        }
        
        /* Barra de colores institucional */
        .color-bar {
            height: 4px;
            background: linear-gradient(to right, #00a651 25%, #f7941d 25%, #f7941d 50%, #00aeef 50%, #00aeef 75%, #ed1c24 75%);
        }
        
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #7f8c8d;
            font-style: italic;
            background-color: #f9fafb;
            border: 1px dashed #e5e5e5;
        }
    </style>
</head>
<body>
    <!-- Header con logo institucional -->
    <div class="header-logo">
        <img src="{{ public_path('images/logo-municipalidad.png') }}" alt="Municipalidad de Arica">
    </div>
    
    <!-- Título del reporte -->
    <div class="header-title">
        <h1>REPORTE DE ESTADÍSTICAS</h1>
        <p>Sistema de Reserva de Recintos Deportivos</p>
        <p class="periodo">Período: <span class="fecha">{{ $desde }}</span> al <span class="fecha">{{ $hasta }}</span></p>
    </div>

    <div class="content">
        <!-- Información general -->
        <div class="info-section">
            <p><strong>Total de Reservas Analizadas:</strong> {{ $totalReservas }}</p>
            <p><strong>Reservas Aprobadas:</strong> {{ $reservasAprobadas }}</p>
            <p><strong>Tasa de Aprobación:</strong> {{ $tasaAprobacion }}%</p>
            <p><strong>Fecha de Generación:</strong> {{ now()->locale('es')->isoFormat('D [de] MMMM [de] YYYY [a las] H:mm') }}</p>
        </div>

        <!-- Estadísticas en cajas -->
        <div class="stats-container">
            <table>
                <tr>
                    <td class="stat-box">
                        <h3>Total de Reservas</h3>
                        <div class="number">{{ $totalReservas }}</div>
                    </td>
                    <td class="stat-box aprobadas">
                        <h3>Reservas Aprobadas</h3>
                        <div class="number">{{ $reservasAprobadas }}</div>
                    </td>
                    <td class="stat-box tasa">
                        <h3>Tasa de Aprobación</h3>
                        <div class="number">{{ $tasaAprobacion }}%</div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Tabla de reservas -->
        @if($reservas->count() > 0)
            <div class="table-section">
                <h2>Detalle de Reservas</h2>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Recinto</th>
                            <th>Deporte</th>
                            <th>Fecha</th>
                            <th>Hora Inicio</th>
                            <th>Hora Fin</th>
                            <th>Organización</th>
                            <th>Estado</th>
                            <th>Participantes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reservas as $reserva)
                            <tr>
                                <td>{{ $reserva->id }}</td>
                                <td>{{ $reserva->recinto->nombre ?? 'N/A' }}</td>
                                <td>{{ $reserva->deporte }}</td>
                                <td>{{ $reserva->fecha_reserva->format('d/m/Y') }}</td>
                                <td>{{ $reserva->hora_inicio->format('H:i') }}</td>
                                <td>{{ $reserva->hora_fin->format('H:i') }}</td>
                                <td>{{ $reserva->nombre_organizacion ?? 'N/A' }}</td>
                                <td>{{ ucfirst($reserva->estado) }}</td>
                                <td>{{ $reserva->cantidad_personas }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <p>No hay reservas disponibles para el período especificado.</p>
            </div>
        @endif
    </div>

    <!-- Footer institucional -->
    <div class="footer">
        <p>Este reporte fue generado automáticamente por el Sistema de Reservas Deportivas</p>
        <p class="light">© {{ date('Y') }} Municipalidad de Arica - Oficina de Deportes</p>
    </div>
    
    <!-- Barra de colores institucional -->
    <div class="color-bar"></div>
</body>
</html>