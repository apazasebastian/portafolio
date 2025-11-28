<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserva Aprobada</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 10px 10px 0 0; text-align: center;">
        <h1 style="margin: 0; font-size: 28px;"> Tu solicitud ha sido confirmada</h1>
    </div>
    
    <div style="background-color: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px;">
        <p style="font-size: 16px; color: #555;">
            Estimado/a <strong>{{ $reserva->representante_nombre ?? $reserva->nombre_organizacion }}</strong>,
        </p>
        
        <p style="font-size: 16px; color: #555;">
            Nos complace informarte que tu solicitud de reserva ha sido:
        </p>
        
        <div style="background-color: #d4edda; color: #155724; padding: 15px; border-radius: 8px; text-align: center; margin: 20px 0;">
            <span style="font-size: 24px; font-weight: bold;"> APROBADA</span>
        </div>

        <!-- Detalles de la Reserva -->
        <h2 style="color: #333; border-bottom: 2px solid #667eea; padding-bottom: 10px; margin-top: 30px;">
             Detalles de tu Reserva
        </h2>
        
        <div style="background-color: white; padding: 20px; border-radius: 8px; margin: 20px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 10px 0; border-bottom: 1px solid #eee;">
                        <strong>🏟️ Recinto:</strong>
                    </td>
                    <td style="padding: 10px 0; border-bottom: 1px solid #eee;">
                        {{ $reserva->recinto->nombre }}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 10px 0; border-bottom: 1px solid #eee;">
                        <strong> Fecha:</strong>
                    </td>
                    <td style="padding: 10px 0; border-bottom: 1px solid #eee;">
                        {{-- CORRECCIÓN CLAVE: Usar format('d/m/Y') en la fecha --}}
                        {{ $reserva->fecha_reserva->format('d/m/Y') }}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 10px 0; border-bottom: 1px solid #eee;">
                        <strong> Horario:</strong>
                    </td>
                    <td style="padding: 10px 0; border-bottom: 1px solid #eee;">
                        {{-- CORRECCIÓN CLAVE: Usar format('H:i') en las horas --}}
                        {{ $reserva->hora_inicio->format('H:i') }} - {{ $reserva->hora_fin->format('H:i') }}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 10px 0; border-bottom: 1px solid #eee;">
                        <strong>👥 Personas:</strong>
                    </td>
                    <td style="padding: 10px 0; border-bottom: 1px solid #eee;">
                        {{-- CORRECCIÓN DE NOMBRE DE CAMPO --}}
                        {{ $reserva->cantidad_personas }}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 10px 0;">
                        <strong>🏢 Organización:</strong>
                    </td>
                    <td style="padding: 10px 0;">
                        {{ $reserva->nombre_organizacion }}
                    </td>
                </tr>
            </table>
        </div>

        <!-- Código de Cancelación -->
        <h2 style="color: #333; border-bottom: 2px solid #f59e0b; padding-bottom: 10px; margin-top: 30px;">
             Código de Cancelación
        </h2>
        
        @if($reserva->codigo_cancelacion)
            <p style="font-size: 16px; color: #555;">
                Si necesitas cancelar tu reserva, utiliza este código único:
            </p>
            
            <div style="background-color: #fef3c7; border: 2px dashed #f59e0b; padding: 20px; margin: 20px 0; text-align: center; border-radius: 8px;">
                <p style="margin: 0 0 5px 0; font-size: 14px; color: #92400e;">Tu código es:</p>
                <p style="margin: 0; font-size: 28px; font-weight: bold; font-family: 'Courier New', monospace; letter-spacing: 3px; color: #d97706;">
                    {{-- El código ahora es corto y legible gracias a la corrección en Reserva.php --}}
                    {{ $reserva->codigo_cancelacion }}
                </p>
            </div>
            
            <div style="background-color: #fff; border-left: 4px solid #f59e0b; padding: 15px; margin: 20px 0;">
                <h3 style="margin: 0 0 10px 0; color: #92400e;">¿Necesitas cancelar?</h3>
                <p style="margin: 5px 0;">Ingresa tu código en nuestro portal de cancelaciones:</p>
                {{-- Asegúrate de que esta ruta 'cancelacion.formulario' esté definida en tu web.php --}}
                <a href="{{ route('cancelacion.formulario') }}" 
                    style="display: inline-block; background-color: #ef4444; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; margin-top: 10px;">
                    Cancelar Mi Reserva
                </a>
            </div>
            
            <p style="font-size: 14px; color: #666; font-style: italic;">
                <strong>Importante:</strong> Guarda este código de manera segura. Lo necesitarás si deseas cancelar tu reserva.
            </p>
        @else
            <div style="background-color: #fee2e2; border: 2px solid #ef4444; padding: 15px; margin: 20px 0; border-radius: 8px;">
                <p style="color: #dc2626; font-weight: bold; margin: 0;">
                     Error: No se pudo generar el código de cancelación. 
                    Por favor, contacta al administrador si necesitas cancelar tu reserva.
                </p>
            </div>
        @endif

        <!-- Instrucciones Importantes -->
        <h2 style="color: #333; border-bottom: 2px solid #667eea; padding-bottom: 10px; margin-top: 30px;">
             Instrucciones Importantes
        </h2>
        
        <ul style="background-color: white; padding: 20px 20px 20px 40px; border-radius: 8px; margin: 20px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <li style="margin-bottom: 10px;">Presentarse <strong>15 minutos antes</strong> del horario reservado</li>
            <li style="margin-bottom: 10px;">Traer documento de identificación</li>
            <li style="margin-bottom: 10px;">Respetar las normas del recinto</li>
            <li style="margin-bottom: 10px;">En caso de cancelación, hazlo con al menos 24 horas de anticipación</li>
        </ul>

        <!-- Información de Contacto -->
        <div style="background-color: #e8f4fd; padding: 20px; border-radius: 8px; margin-top: 30px; text-align: center;">
            <h3 style="color: #333; margin-top: 0;"> ¿Necesitas ayuda?</h3>
            <p style="margin: 5px 0;">Contáctanos:</p>
            <p style="margin: 5px 0;"><strong>Teléfono:</strong> +56 58 220 5522</p>
            <p style="margin: 5px 0;"><strong>Email:</strong> deportes@municipalidaddearica.cl</p>
        </div>

        <!-- Pie del correo -->
        <div style="margin-top: 40px; padding-top: 20px; border-top: 2px solid #eee; text-align: center; color: #888;">
            <p style="margin: 5px 0;">Este es un correo automático, por favor no responder.</p>
            <p style="margin: 5px 0;">© {{ date('Y') }} Municipalidad de Arica - Oficina de Deportes</p>
        </div>
    </div>
</body>
</html>