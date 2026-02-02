<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Consulta de Contacto</title>
</head>
<body style="font-family: 'Segoe UI', Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 0; background-color: #f5f5f5;">
    
    <!-- Header con logo institucional -->
    <div style="background-color: #ffffff; padding: 0; text-align: center;">
        <img src="{{ $message->embed(public_path('images/nuevo-logo-municipalidad.png')) }}" alt="Municipalidad de Arica" style="width: 100%; max-width: 600px; height: auto; display: block;">
    </div>
    
    <!-- Contenido principal -->
    <div style="background-color: #ffffff; padding: 30px;">
        
        <!-- Banner de estado -->
        <div style="background-color: #1a1a8e; color: white; padding: 20px; text-align: center; margin-bottom: 25px;">
            <h1 style="margin: 0; font-size: 22px; font-weight: 600; letter-spacing: 0.5px;">
                 NUEVA CONSULTA DE CONTACTO
            </h1>
        </div>
        
        <p style="font-size: 15px; color: #444; margin-bottom: 20px;">
            Se ha recibido una nueva consulta a través del formulario de contacto web.
        </p>

        <!-- Recinto de Interés -->
        <div style="background-color: #1a1a8e; padding: 15px; margin: 20px 0; text-align: center;">
            <p style="color: #ffffff; margin: 0; font-size: 14px; text-transform: uppercase; letter-spacing: 1px;">
                Recinto de Interés
            </p>
            <p style="color: #ffffff; margin: 5px 0 0 0; font-size: 18px; font-weight: bold;">
                {{ $datos['nombreRecinto'] }}
            </p>
        </div>

        <!-- Datos del Contacto -->
        <div style="background-color: #f8f9fa; border-left: 4px solid #1a1a8e; padding: 20px; margin: 20px 0;">
            <h2 style="color: #1a1a8e; margin: 0 0 15px 0; font-size: 16px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">
                Datos del Contacto
            </h2>
            
            <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                <tr>
                    <td style="padding: 8px 0; color: #666; width: 40%;">Nombre y Apellido:</td>
                    <td style="padding: 8px 0; color: #333; font-weight: 500;">{{ $datos['nombre'] }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #666;">Email:</td>
                    <td style="padding: 8px 0; color: #333; font-weight: 500;">
                        <a href="mailto:{{ $datos['email'] }}" style="color: #1a1a8e; text-decoration: underline;">
                            {{ $datos['email'] }}
                        </a>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #666;">Teléfono:</td>
                    <td style="padding: 8px 0; color: #333; font-weight: 500;">{{ $datos['telefono'] ?? 'No proporcionado' }}</td>
                </tr>
            </table>
        </div>

        <!-- Mensaje -->
        <div style="background-color: #f8f9fa; padding: 20px; margin: 20px 0;">
            <h2 style="color: #1a1a8e; margin: 0 0 15px 0; font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">
                Mensaje
            </h2>
            <p style="font-size: 14px; color: #333; margin: 0; white-space: pre-line;">{{ $datos['mensaje'] }}</p>
        </div>

        <!-- Instrucciones -->
        <div style="background-color: #f0f4f8; padding: 20px; margin: 20px 0; border-left: 4px solid #ff9800;">
            <h2 style="color: #ff9800; margin: 0 0 10px 0; font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">
                 Acción Requerida
            </h2>
            <p style="font-size: 14px; color: #555; margin: 0;">
                Por favor, responde a esta consulta dentro de las próximas 24-48 horas. 
                Puedes responder directamente al email del usuario haciendo clic en su dirección de correo.
            </p>
        </div>

        <!-- Contacto -->
        <div style="text-align: center; padding: 20px; background-color: #f0f4f8; margin-top: 25px;">
            <p style="margin: 0; font-size: 14px; color: #333;">
                <strong>Sistema de Reservas Deportivas</strong><br>
                Municipalidad de Arica
            </p>
        </div>
    </div>

    <!-- Footer institucional -->
    <div style="padding: 0; text-align: center;">
        <img src="{{ $message->embed(public_path('images/footer-municipalidad.png')) }}" alt="Footer Municipalidad de Arica" style="width: 100%; max-width: 600px; height: auto; display: block;">
    </div>

</body>
</html>
