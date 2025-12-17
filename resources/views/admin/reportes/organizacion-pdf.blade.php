<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte - {{ $organizacion['nombre'] }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #333;
        }
        
        .container {
            padding: 20px;
        }
        
        /* Header */
        .header {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            color: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
        }
        
        .header h1 {
            font-size: 20pt;
            margin-bottom: 5px;
        }
        
        .header p {
            font-size: 10pt;
            opacity: 0.9;
        }
        
        .fecha-generacion {
            text-align: right;
            font-size: 9pt;
            color: #666;
            margin-bottom: 15px;
        }
        
        /* Info Organización */
        .info-org {
            background: #f3f4f6;
            padding: 15px;
            border-left: 4px solid #2563eb;
            margin-bottom: 20px;
        }
        
        .info-org h2 {
            font-size: 14pt;
            color: #1f2937;
            margin-bottom: 8px;
        }
        
        .info-org p {
            font-size: 9pt;
            color: #4b5563;
            margin: 3px 0;
        }
        
        /* Estadísticas Grid */
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .stat-card {
            display: table-cell;
            width: 25%;
            padding: 12px;
            text-align: center;
            border: 1px solid #e5e7eb;
        }
        
        .stat-card.green { background: #dcfce7; border-color: #10b981; }
        .stat-card.yellow { background: #fef3c7; border-color: #f59e0b; }
        .stat-card.red { background: #fee2e2; border-color: #ef4444; }
        .stat-card.blue { background: #dbeafe; border-color: #3b82f6; }
        
        .stat-label {
            font-size: 8pt;
            color: #6b7280;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .stat-value {
            font-size: 20pt;
            font-weight: bold;
            margin-bottom: 3px;
        }
        
        .stat-card.green .stat-value { color: #059669; }
        .stat-card.yellow .stat-value { color: #d97706; }
        .stat-card.red .stat-value { color: #dc2626; }
        .stat-card.blue .stat-value { color: #2563eb; }
        
        .stat-extra {
            font-size: 7pt;
            color: #6b7280;
        }
        
        /* Secciones */
        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        
        .section-title {
            font-size: 13pt;
            font-weight: bold;
            color: #1f2937;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 5px;
            margin-bottom: 12px;
        }
        
        /* Análisis de Patrones */
        .patrones-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        
        .patron-item {
            display: table-cell;
            width: 33.33%;
            padding: 10px;
            border-left: 3px solid #3b82f6;
            background: #f9fafb;
        }
        
        .patron-label {
            font-size: 8pt;
            color: #6b7280;
            margin-bottom: 3px;
        }
        
        .patron-value {
            font-size: 12pt;
            font-weight: bold;
            color: #1f2937;
        }
        
        /* Top Recintos */
        .recinto-item {
            margin-bottom: 12px;
            page-break-inside: avoid;
        }
        
        .recinto-header {
            display: table;
            width: 100%;
            margin-bottom: 3px;
        }
        
        .recinto-rank {
            display: table-cell;
            width: 30px;
            font-size: 14pt;
            font-weight: bold;
            color: #9ca3af;
            text-align: center;
        }
        
        .recinto-info {
            display: table-cell;
            font-size: 9pt;
        }
        
        .recinto-nombre {
            font-weight: 600;
            color: #1f2937;
        }
        
        .recinto-stats {
            color: #6b7280;
            font-size: 8pt;
        }
        
        .progress-bar {
            width: 100%;
            height: 8px;
            background: #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
            margin-left: 30px;
        }
        
        .progress-fill {
            height: 100%;
            background: #3b82f6;
        }
        
        /* Tabla de Reservas */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
            margin-bottom: 15px;
        }
        
        thead {
            background: #f3f4f6;
        }
        
        th {
            padding: 8px 5px;
            text-align: left;
            font-weight: 600;
            color: #374151;
            border-bottom: 2px solid #d1d5db;
            font-size: 7pt;
            text-transform: uppercase;
        }
        
        td {
            padding: 6px 5px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        tbody tr:nth-child(even) {
            background: #f9fafb;
        }
        
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7pt;
            font-weight: 600;
        }
        
        .badge-aprobada {
            background: #dcfce7;
            color: #166534;
        }
        
        .badge-pendiente {
            background: #fef3c7;
            color: #92400e;
        }
        
        .badge-rechazada {
            background: #fee2e2;
            color: #991b1b;
        }
        
        /* Footer */
        .footer {
            position: fixed;
            bottom: 15px;
            left: 20px;
            right: 20px;
            text-align: center;
            font-size: 7pt;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 8px;
        }
        
        .page-number:after {
            content: "Página " counter(page);
        }
        
        /* Page Break */
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>Reporte Histórico por Organización</h1>
            <p>Análisis detallado del uso de recintos deportivos</p>
        </div>
        
        <div class="fecha-generacion">
            Fecha de generación: {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}
        </div>
        
        <!-- Información de la Organización -->
        <div class="info-org">
            <h2>{{ $organizacion['nombre'] }}</h2>
            <p><strong>Representante:</strong> {{ $organizacion['representante'] }}</p>
            <p><strong>Email:</strong> {{ $organizacion['email'] }}</p>
            <p><strong>Teléfono:</strong> {{ $organizacion['telefono'] }}</p>
            <p><strong>RUT:</strong> {{ $organizacion['rut'] }}</p>
        </div>
        
        <!-- Estadísticas Generales -->
        <div class="stats-grid">
            <div class="stat-card green">
                <div class="stat-label">Aprobadas</div>
                <div class="stat-value">{{ $estadisticas['aprobadas'] }}</div>
                <div class="stat-extra">{{ $estadisticas['pct_aprobadas'] }}% del total</div>
            </div>
            <div class="stat-card yellow">
                <div class="stat-label">Pendientes</div>
                <div class="stat-value">{{ $estadisticas['pendientes'] }}</div>
                <div class="stat-extra">{{ $estadisticas['pct_pendientes'] }}% del total</div>
            </div>
            <div class="stat-card red">
                <div class="stat-label">Rechazadas</div>
                <div class="stat-value">{{ $estadisticas['rechazadas'] }}</div>
                <div class="stat-extra">{{ $estadisticas['pct_rechazadas'] }}% del total</div>
            </div>
            <div class="stat-card blue">
                <div class="stat-label">Horas Totales</div>
                <div class="stat-value">{{ $estadisticas['horas_totales'] }}</div>
                <div class="stat-extra">{{ $estadisticas['horas_promedio'] }}h promedio</div>
            </div>
        </div>
        
        <!-- Análisis de Patrones -->
        <div class="section">
            <div class="section-title">Análisis de Patrones de Uso</div>
            <div class="patrones-grid">
                <div class="patron-item">
                    <div class="patron-label">Día Favorito</div>
                    <div class="patron-value">{{ $analisis['dia_semana_favorito'] }}</div>
                </div>
                <div class="patron-item">
                    <div class="patron-label">Horario Preferido</div>
                    <div class="patron-value">{{ $analisis['horario_favorito'] }}</div>
                </div>
                <div class="patron-item">
                    <div class="patron-label">Tasa de Aprobación</div>
                    <div class="patron-value">{{ $analisis['tasa_aprobacion'] }}%</div>
                </div>
            </div>
        </div>
        
        <!-- Deportes Más Solicitados -->
        <div class="section">
            <div class="section-title"> Deportes Más Solicitados</div>
            <table>
                <thead>
                    <tr>
                        <th width="10%">#</th>
                        <th width="60%">Deporte</th>
                        <th width="30%" style="text-align: right;">Cantidad de Reservas</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($deportes as $index => $deporte)
                    <tr>
                        <td style="text-align: center; font-weight: bold; color: #6b7280;">{{ $index + 1 }}</td>
                        <td>{{ $deporte['nombre'] }}</td>
                        <td style="text-align: right; font-weight: 600;">{{ $deporte['cantidad'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Top Recintos -->
        <div class="section">
            <div class="section-title"> Recintos Más Utilizados</div>
            @foreach($recintos_top as $index => $recinto)
            <div class="recinto-item">
                <div class="recinto-header">
                    <div class="recinto-rank">{{ $index + 1 }}</div>
                    <div class="recinto-info">
                        <div class="recinto-nombre">{{ $recinto['nombre'] }}</div>
                        <div class="recinto-stats">{{ $recinto['cantidad'] }} reservas ({{ $recinto['porcentaje'] }}%)</div>
                    </div>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ $recinto['porcentaje'] }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- Salto de página antes de la tabla -->
        <div class="page-break"></div>
        
        <!-- Historial de Reservas -->
        <div class="section">
            <div class="section-title"> Historial Completo de Reservas ({{ count($reservas) }} total)</div>
            <table>
                <thead>
                    <tr>
                        <th width="8%">ID</th>
                        <th width="12%">Fecha</th>
                        <th width="15%">Horario</th>
                        <th width="25%">Recinto</th>
                        <th width="15%">Deporte</th>
                        <th width="10%">Personas</th>
                        <th width="15%">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reservas as $reserva)
                    <tr>
                        <td style="font-weight: 600;">#{{ $reserva['id'] }}</td>
                        <td>{{ $reserva['fecha_formato'] }}</td>
                        <td>{{ $reserva['hora_inicio'] }} - {{ $reserva['hora_fin'] }}</td>
                        <td>{{ $reserva['recinto'] }}</td>
                        <td>{{ $reserva['deporte'] }}</td>
                        <td style="text-align: center;">{{ $reserva['personas'] }}</td>
                        <td>
                            <span class="badge badge-{{ $reserva['estado'] }}">
                                {{ ucfirst($reserva['estado']) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Resumen Final -->
        <div class="section">
            <div class="section-title"> Resumen Ejecutivo</div>
            <p style="margin-bottom: 8px;">
                <strong>Total de Reservas:</strong> {{ $estadisticas['total'] }} reservas registradas
            </p>
            <p style="margin-bottom: 8px;">
                <strong>Tasa de Aprobación:</strong> {{ $analisis['tasa_aprobacion'] }}% de las reservas procesadas fueron aprobadas
            </p>
            <p style="margin-bottom: 8px;">
                <strong>Deporte Favorito:</strong> {{ $analisis['deporte_favorito'] ?? 'N/A' }}
            </p>
            <p style="margin-bottom: 8px;">
                <strong>Recinto Favorito:</strong> {{ $analisis['recinto_favorito'] ?? 'N/A' }}
            </p>
            <p style="margin-bottom: 8px;">
                <strong>Patrón de Uso:</strong> Principalmente {{ $analisis['dia_semana_favorito'] }} en horario de {{ $analisis['horario_favorito'] }}
            </p>
        </div>
    </div>
    
    <!-- Footer -->
    <div class="footer">
        <div class="page-number"></div>
        <div>Sistema de Gestión de Recintos Deportivos - Generado automáticamente</div>
    </div>
</body>
</html>