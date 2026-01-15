<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserva Rechazada</title>
</head>
<body style="font-family: 'Segoe UI', Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 0; background-color: #f5f5f5;">
    
    <!-- Header con logo institucional -->
    <div style="background-color: #ffffff; padding: 20px 30px; text-align: center; border-bottom: 4px solid #1a1a8e;">
        <img src="{{ $message->embed(public_path('images/logo-municipalidad.png')) }}" alt="Municipalidad de Arica" style="max-height: 60px; width: auto;">
    </div>
    
    <!-- Contenido principal -->
    <div style="background-color: #ffffff; padding: 30px;">
        
        <!-- Banner de estado -->
        <div style="background-color: #1a1a8e; color: white; padding: 20px; text-align: center; margin-bottom: 25px;">
            <h1 style="margin: 0; font-size: 22px; font-weight: 600; letter-spacing: 0.5px;">
                ✕ SOLICITUD RECHAZADA
            </h1>
        </div>
        
        <p style="font-size: 15px; color: #444; margin-bottom: 20px;">
            Estimado/a <strong>{{ $reserva->representante_nombre ?? $reserva->nombre_organizacion }}</strong>,
        </p>
        
        <p style="font-size: 15px; color: #444; margin-bottom: 25px;">
            Lamentamos informarle que su solicitud de reserva ha sido <strong style="color: #dc2626;">rechazada</strong>.
        </p>

        <!-- Motivo del Rechazo -->
        <div style="background-color: #fef2f2; border-left: 4px solid #dc2626; padding: 20px; margin: 20px 0;">
            <h2 style="color: #dc2626; margin: 0 0 10px 0; font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">
                Motivo del Rechazo
            </h2>
            <p style="color: #991b1b; margin: 0; font-size: 14px;">
                {{ $reserva->motivo_rechazo }}
            </p>
        </div>

        <!-- Detalles de la Solicitud -->
        <div style="background-color: #eff6ff; border-left: 4px solid #1a1a8e; padding: 20px; margin: 20px 0;">
            <h2 style="color: #1a1a8e; margin: 0 0 15px 0; font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">
                Detalles de la Solicitud
            </h2>
            
            <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                <tr>
                    <td style="padding: 8px 0; color: #666; width: 40%;">Recinto:</td>
                    <td style="padding: 8px 0; color: #333; font-weight: 500;">{{ $reserva->recinto->nombre }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #666;">Fecha:</td>
                    <td style="padding: 8px 0; color: #333; font-weight: 500;">{{ $reserva->fecha_reserva->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #666;">Horario:</td>
                    <td style="padding: 8px 0; color: #333; font-weight: 500;">
                        {{ \Carbon\Carbon::parse($reserva->hora_inicio)->format('H:i') }} - 
                        {{ \Carbon\Carbon::parse($reserva->hora_fin)->format('H:i') }}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #666;">Organización:</td>
                    <td style="padding: 8px 0; color: #333; font-weight: 500;">{{ $reserva->nombre_organizacion }}</td>
                </tr>
            </table>
        </div>

        <!-- Información de ayuda -->
        <div style="background-color: #eff6ff; border-left: 4px solid #1a1a8e; padding: 20px; margin: 20px 0;">
            <p style="color: #1a1a8e; margin: 0; font-size: 14px;">
                <strong>¿Necesita ayuda?</strong><br>
                Puede realizar una nueva solicitud considerando el motivo del rechazo, 
                o contactarnos para más información.
            </p>
        </div>

        <!-- Contacto -->
        <div style="text-align: center; padding: 20px; background-color: #f0f4f8; margin-top: 25px;">
            <p style="margin: 0 0 10px 0; font-size: 14px; color: #666;">Contáctenos</p>
            <p style="margin: 0; font-size: 14px; color: #333;">
                <strong>Teléfono:</strong> +56 58 220 5522 | 
                <strong>Email:</strong> deportes@municipalidaddearica.cl
            </p>
        </div>
    </div>

    <!-- Footer institucional -->
    <div style="background-color: #1a1a8e; padding: 20px 30px; text-align: center;">
        <p style="color: #ffffff; font-size: 12px; margin: 0 0 5px 0;">
            Este es un correo automático, por favor no responder.
        </p>
        <p style="color: rgba(255,255,255,0.7); font-size: 11px; margin: 0;">
            © {{ date('Y') }} Municipalidad de Arica - Oficina de Deportes
        </p>
    </div>

    <!-- Barra de colores institucional -->
    <div style="height: 4px; background: linear-gradient(to right, #00a651 25%, #f7941d 25%, #f7941d 50%, #00aeef 50%, #00aeef 75%, #ed1c24 75%);"></div>

</body>
</html>