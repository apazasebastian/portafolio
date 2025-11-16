<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f3f4f6;
            line-height: 1.6;
        }
        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .header-icon {
            width: 80px;
            height: 80px;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-center;
            margin: 0 auto 20px;
            font-size: 40px;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }
        .header p {
            margin: 10px 0 0;
            opacity: 0.9;
            font-size: 16px;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            color: #374151;
            margin-bottom: 20px;
        }
        .status-badge {
            display: inline-block;
            background-color: #e5e7eb;
            color: #1f2937;
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 14px;
            margin: 20px 0;
        }
        .details-box {
            background-color: #f9fafb;
            border-left: 4px solid #6b7280;
            padding: 25px;
            margin: 30px 0;
            border-radius: 8px;
        }
        .details-box h3 {
            margin: 0 0 20px;
            color: #111827;
            font-size: 18px;
        }
        .detail-item {
            display: flex;
            padding: 12px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .detail-item:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #6b7280;
            width: 120px;
            flex-shrink: 0;
        }
        .detail-value {
            color: #111827;
            font-weight: 500;
        }
        .motivo-box {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 20px;
            margin: 25px 0;
            border-radius: 8px;
        }
        .motivo-box h4 {
            margin: 0 0 10px;
            color: #92400e;
            font-size: 16px;
        }
        .motivo-box p {
            margin: 0;
            color: #78350f;
            font-size: 15px;
        }
        .info-box {
            background-color: #dbeafe;
            border-left: 4px solid #3b82f6;
            padding: 20px;
            margin: 25px 0;
            border-radius: 8px;
        }
        .info-box p {
            margin: 0;
            color: #1e40af;
            font-size: 15px;
        }
        .footer {
            background-color: #f9fafb;
            padding: 30px;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
        }
        .footer strong {
            color: #111827;
            display: block;
            margin-top: 10px;
        }
        .divider {
            height: 1px;
            background-color: #e5e7eb;
            margin: 30px 0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        
        <!-- Header con icono -->
        <div class="header">
            <div class="header-icon">
                ✕
            </div>
            <h1>Reserva Cancelada</h1>
            <p>Confirmación de cancelación</p>
        </div>

        <!-- Contenido -->
        <div class="content">
            <p class="greeting">Estimado/a <strong>{{ $reserva->representante_nombre }}</strong>,</p>
            
            <p>Te confirmamos que tu reserva ha sido:</p>
            
            <div style="text-align: center;">
                <span class="status-badge">CANCELADA EXITOSAMENTE</span>
            </div>

            <!-- Detalles de la reserva cancelada -->
            <div class="details-box">
                <h3>📋 Detalles de la Reserva Cancelada</h3>
                
                <div class="detail-item">
                    <span class="detail-label">🏟️ Recinto:</span>
                    <span class="detail-value">{{ $reserva->recinto->nombre }}</span>
                </div>
                
                <div class="detail-item">
                    <span class="detail-label">📅 Fecha:</span>
                    <span class="detail-value">{{ $reserva->fecha_reserva->format('d/m/Y') }}</span>
                </div>
                
                <div class="detail-item">
                    <span class="detail-label">🕐 Horario:</span>
                    <span class="detail-value">{{ $reserva->hora_inicio }} - {{ $reserva->hora_fin }}</span>
                </div>
                
                <div class="detail-item">
                    <span class="detail-label">🏢 Organización:</span>
                    <span class="detail-value">{{ $reserva->nombre_organizacion }}</span>
                </div>
                
                <div class="detail-item">
                    <span class="detail-label">📆 Cancelada el:</span>
                    <span class="detail-value">{{ $reserva->fecha_cancelacion->format('d/m/Y H:i') }}</span>
                </div>
            </div>

            <!-- Motivo de la cancelación -->
            @if($reserva->motivo_cancelacion)
            <div class="motivo-box">
                <h4>📝 Motivo de la Cancelación</h4>
                <p>{{ $reserva->motivo_cancelacion }}</p>
            </div>
            @endif

            <!-- Información adicional -->
            <div class="info-box">
                <p>
                    <strong>💡 Información importante:</strong><br>
                    El horario quedará disponible para otras organizaciones. 
                    Si necesitas realizar una nueva reserva, puedes hacerlo ingresando a nuestro sistema de reservas.
                </p>
            </div>

            <div class="divider"></div>

            <p style="color: #6b7280; font-size: 14px; text-align: center;">
                Si tienes alguna consulta, puedes responder a este correo.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="margin: 0;">Saludos cordiales,</p>
            <strong>Municipalidad de Arica</strong>
            <p style="margin: 5px 0 0;">Departamento de Deportes y Recreación</p>
        </div>

    </div>
</body>
</html>