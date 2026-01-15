<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserva Aprobada</title>
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
                ✓ SOLICITUD APROBADA
            </h1>
        </div>
        
        <p style="font-size: 15px; color: #444; margin-bottom: 20px;">
            Estimado/a <strong>{{ $reserva->representante_nombre ?? $reserva->nombre_organizacion }}</strong>,
        </p>
        
        <p style="font-size: 15px; color: #444; margin-bottom: 25px;">
            Nos complace informarle que su solicitud de reserva ha sido <strong style="color: #1a1a8e;">aprobada</strong>. 
            A continuación encontrará los detalles de su reserva.
        </p>

        <!-- Detalles de la Reserva -->
        <div style="background-color: #f8f9fa; border-left: 4px solid #1a1a8e; padding: 20px; margin: 20px 0;">
            <h2 style="color: #1a1a8e; margin: 0 0 15px 0; font-size: 16px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">
                Detalles de la Reserva
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
                    <td style="padding: 8px 0; color: #333; font-weight: 500;">{{ $reserva->hora_inicio->format('H:i') }} - {{ $reserva->hora_fin->format('H:i') }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #666;">Personas:</td>
                    <td style="padding: 8px 0; color: #333; font-weight: 500;">{{ $reserva->cantidad_personas }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #666;">Organización:</td>
                    <td style="padding: 8px 0; color: #333; font-weight: 500;">{{ $reserva->nombre_organizacion }}</td>
                </tr>
            </table>
        </div>

        <!-- Encargado del Recinto -->
        <div style="background-color: #1a1a8e; padding: 20px; margin: 20px 0;">
            <h2 style="color: #ffffff; margin: 0 0 15px 0; font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">
                Encargado del Recinto
            </h2>
            
            @php
                $encargados = [
                    1 => ['nombre' => 'Carlos Pérez', 'email' => 'carlosapazac33@gmail.com', 'telefono' => '+56 9 2245 8901'],
                    2 => ['nombre' => 'María García', 'email' => 'maria.garcia@munirica.cl', 'telefono' => '+56 9 3156 7234'],
                    3 => ['nombre' => 'Roberto Flores', 'email' => 'roberto.flores@munirica.cl', 'telefono' => '+56 9 4567 1289'],
                    4 => ['nombre' => 'Andrea Castillo', 'email' => 'andrea.castillo@munirica.cl', 'telefono' => '+56 9 8765 4321'],
                ];
                $encargado = $encargados[$reserva->recinto_id] ?? null;
            @endphp
            
            <table style="width: 100%; border-collapse: collapse; font-size: 14px; color: #ffffff;">
                <tr>
                    <td style="padding: 6px 0; width: 35%;">Nombre:</td>
                    <td style="padding: 6px 0;">{{ $encargado['nombre'] ?? 'Encargado del Recinto' }}</td>
                </tr>
                <tr>
                    <td style="padding: 6px 0;">Correo:</td>
                    <td style="padding: 6px 0;">
                        <a href="mailto:{{ $encargado['email'] ?? 'deportes@municipalidaddearica.cl' }}" style="color: #ffffff; text-decoration: underline;">
                            {{ $encargado['email'] ?? 'deportes@municipalidaddearica.cl' }}
                        </a>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 6px 0;">Teléfono:</td>
                    <td style="padding: 6px 0;">{{ $encargado['telefono'] ?? '+56 58 220 5522' }}</td>
                </tr>
            </table>
        </div>

        <!-- Código de Cancelación -->
        @if($reserva->codigo_cancelacion)
        <div style="border: 2px solid #e5e5e5; padding: 20px; margin: 20px 0; text-align: center;">
            <p style="color: #666; font-size: 13px; margin: 0 0 10px 0; text-transform: uppercase; letter-spacing: 1px;">
                Código de Cancelación
            </p>
            <p style="font-size: 24px; font-weight: bold; font-family: 'Courier New', monospace; letter-spacing: 3px; color: #1a1a8e; margin: 0;">
                {{ $reserva->codigo_cancelacion }}
            </p>
            <p style="font-size: 12px; color: #888; margin: 10px 0 0 0;">
                Guarde este código si necesita cancelar su reserva
            </p>
        </div>
        
        <div style="text-align: center; margin: 20px 0;">
            <a href="{{ route('cancelacion.formulario') }}" 
               style="display: inline-block; background-color: #dc2626; color: white; padding: 12px 30px; text-decoration: none; font-size: 14px; font-weight: 500; letter-spacing: 0.5px;">
                CANCELAR RESERVA
            </a>
        </div>
        @endif

        <!-- Instrucciones -->
        <div style="background-color: #f8f9fa; padding: 20px; margin: 20px 0;">
            <h2 style="color: #1a1a8e; margin: 0 0 15px 0; font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">
                Instrucciones Importantes
            </h2>
            <ul style="padding-left: 20px; margin: 0; font-size: 14px; color: #555;">
                <li style="margin-bottom: 8px;">Presentarse 15 minutos antes del horario reservado</li>
                <li style="margin-bottom: 8px;">Traer documento de identificación</li>
                <li style="margin-bottom: 8px;">Respetar las normas del recinto</li>
                <li style="margin-bottom: 0;">En caso de cancelación, hacerlo con al menos 24 horas de anticipación</li>
            </ul>
        </div>

        <!-- Contacto -->
        <div style="text-align: center; padding: 20px; background-color: #f0f4f8; margin-top: 25px;">
            <p style="margin: 0 0 10px 0; font-size: 14px; color: #666;">¿Necesita ayuda?</p>
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