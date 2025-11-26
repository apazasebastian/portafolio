<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Estadísticas</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.6;
        }
        
        .header {
            background-color: #2c3e50;
            color: white;
            padding: 30px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 28px;
            margin-bottom: 5px;
        }
        
        .header p {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .info-section {
            background-color: #ecf0f1;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .info-section p {
            margin-bottom: 5px;
            font-size: 13px;
        }
        
        .stats-container {
            display: flex;
            justify-content: space-around;
            margin-bottom: 30px;
            gap: 10px;
        }
        
        .stat-box {
            flex: 1;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
        }
        
        .stat-box.aprobadas {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }
        
        .stat-box.rechazadas {
            background: linear-gradient(135deg, #ee0979 0%, #ff6a00 100%);
        }
        
        .stat-box h3 {
            font-size: 12px;
            opacity: 0.9;
            margin-bottom: 10px;
        }
        
        .stat-box .number {
            font-size: 32px;
            font-weight: bold;
        }
        
        .stat-box .percentage {
            font-size: 13px;
            margin-top: 5px;
            opacity: 0.9;
        }
        
        .table-section {
            margin-top: 30px;
            page-break-inside: avoid;
        }
        
        .table-section h2 {
            background-color: #34495e;
            color: white;
            padding: 12px;
            margin-bottom: 15px;
            font-size: 16px;
            border-radius: 3px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 11px;
        }
        
        thead {
            background-color: #4472c4;
            color: white;
        }
        
        th {
            padding: 10px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #bdc3c7;
        }
        
        td {
            padding: 8px 10px;
            border: 1px solid #ecf0f1;
            background-color: white;
        }
        
        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        tbody tr:hover {
            background-color: #ecf0f1;
        }
        
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #bdc3c7;
            color: #7f8c8d;
            font-size: 12px;
        }
        
        .footer p {
            margin-bottom: 5px;
        }
        
        .fecha {
            font-weight: bold;
            color: #2c3e50;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #7f8c8d;
            font-style: italic;
        }
    </style>
</head>
<body>
    <!-- ENCABEZADO -->
    <div class="header">
        <h1>Reporte de Estadísticas</h1>
        <p>Reservas Deportivas - Arica</p>
        <p>Período: <span class="fecha">{{ $desde }}</span> al <span class="fecha">{{ $hasta }}</span></p>
    </div>

    <!-- INFORMACIÓN GENERAL -->
    <div class="info-section">
        <p><strong>Total de Reservas Analizadas:</strong> {{ $totalReservas }}</p>
        <p><strong>Reservas Aprobadas:</strong> {{ $reservasAprobadas }}</p>
        <p><strong>Tasa de Aprobación:</strong> {{ $tasaAprobacion }}%</p>
        <p><strong>Fecha de Generación del Reporte:</strong> {{ now()->locale('es')->isoFormat('D [de] MMMM [de] YYYY [a las] H:mm') }}</p>
    </div>

    <!-- ESTADÍSTICAS EN CAJAS -->
    <div class="stats-container">
        <div class="stat-box">
            <h3>Total de Reservas</h3>
            <div class="number">{{ $totalReservas }}</div>
        </div>
        <div class="stat-box aprobadas">
            <h3>Reservas Aprobadas</h3>
            <div class="number">{{ $reservasAprobadas }}</div>
            <div class="percentage">{{ $tasaAprobacion }}%</div>
        </div>
        <div class="stat-box rechazadas">
            <h3>Tasa de Aprobación</h3>
            <div class="number">{{ $tasaAprobacion }}%</div>
        </div>
    </div>

    <!-- TABLA DE RESERVAS -->
    @if($reservas->count() > 0)
        <div class="table-section">
            <h2>Detalle de Reservas</h2>
            <table>
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

    <!-- PIE DE PÁGINA -->
    <div class="footer">
        <p>Este reporte fue generado automáticamente por el Sistema de Reservas Deportivas</p>
        <p>Para más información, contacte al administrador del sistema</p>
    </div>
</body>
</html>