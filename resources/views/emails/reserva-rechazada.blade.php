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
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
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
            justify-content: center;
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
            background-color: #fee2e2;
            color: #991b1b;
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 14px;
            margin: 20px 0;
        }
        .reason-box {
            background-color: #fef2f2;
            border-left: 4px solid #dc2626;
            padding: 25px;
            margin: 30px 0;
            border-radius: 8px;
        }
        .reason-box h3 {
            margin: 0 0 15px;
            color: #991b1b;
            font-size: 18px;
        }
        .reason-box p {
            color: #7f1d1d;
            margin: 0;
            font-size: 15px;
            line-height: 1.6;
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
        .help-box {
            background-color: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 20px;
            margin: 25px 0;
            border-radius: 8px;
        }
        .help-box p {
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
        
        <div class="header">
            <div class="header-icon">
                ✕
            </div>
            <h1>Reserva Rechazada</h1>
            <p>Información sobre tu solicitud</p>
        </div>

        <div class="content">
            <p class="greeting">Estimado/a <strong>{{ $reserva->representante_nombre }}</strong>,</p>
            
            <p>Lamentamos informarte que tu solicitud de reserva ha sido:</p>
            
            <div style="text-align: center;">
                <span class="status-badge">✕ RECHAZADA</span>
            </div>

            <div class="reason-box">
                <h3>📝 Motivo del rechazo</h3>
                <p>{{ $reserva->motivo_rechazo }}</p>
            </div>

            <div class="details-box">
                <h3>📋 Detalles de tu Solicitud</h3>
                
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
                    <span class="detail-value">
                        {{ \Carbon\Carbon::parse($reserva->hora_inicio)->format('H:i') }} - 
                        {{ \Carbon\Carbon::parse($reserva->hora_fin)->format('H:i') }}
                    </span>
                </div>
                
                <div class="detail-item">
                    <span class="detail-label">🏢 Organización:</span>
                    <span class="detail-value">{{ $reserva->nombre_organizacion }}</span>
                </div>
            </div>

            <div class="help-box">
                <p>
                    💡 <strong>¿Necesitas ayuda?</strong><br>
                    Puedes realizar una nueva solicitud considerando el motivo del rechazo, 
                    o contactarnos respondiendo a este correo para más información.
                </p>
            </div>

            <div class="divider"></div>

            <p style="color: #6b7280; font-size: 14px; text-align: center;">
                Agradecemos tu comprensión.
            </p>
        </div>

        <div class="footer">
            <p style="margin: 0;">Saludos cordiales,</p>
            <strong>Municipalidad de Arica</strong>
            <p style="margin: 5px 0 0;">Departamento de Deportes y Recreación</p>
        </div>

    </div>
</body>
</html>