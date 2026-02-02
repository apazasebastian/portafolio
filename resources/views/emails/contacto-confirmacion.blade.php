<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Contacto</title>
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
                ✓ MENSAJE RECIBIDO
            </h1>
        </div>
        
        <p style="font-size: 15px; color: #444; margin-bottom: 20px;">
            Estimado/a <strong>{{ $nombreUsuario }}</strong>,
        </p>
        
        <p style="font-size: 15px; color: #444; margin-bottom: 25px;">
            Hemos recibido tu mensaje de consulta sobre <strong style="color: #1a1a8e;">{{ $nombreRecinto }}</strong>. 
            El encargado del recinto se pondrá en contacto contigo a la brevedad.
        </p>

        <!-- Información del seguimiento -->
        <div style="background-color: #f8f9fa; border-left: 4px solid #1a1a8e; padding: 20px; margin: 20px 0;">
            <h2 style="color: #1a1a8e; margin: 0 0 15px 0; font-size: 16px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">
                Próximos Pasos
            </h2>
            
            <ul style="padding-left: 20px; margin: 0; font-size: 14px; color: #555;">
                <li style="margin-bottom: 8px;">El encargado del recinto revisará tu consulta</li>
                <li style="margin-bottom: 8px;">Recibirás una respuesta en un plazo de 24 a 48 horas</li>
                <li style="margin-bottom: 0;">Si tienes una consulta urgente, puedes llamar al teléfono indicado abajo</li>
            </ul>
        </div>

        <!-- Contacto -->
        <div style="text-align: center; padding: 20px; background-color: #f0f4f8; margin-top: 25px;">
            <p style="margin: 0 0 10px 0; font-size: 14px; color: #666;">¿Necesita ayuda inmediata?</p>
            <p style="margin: 0; font-size: 14px; color: #333;">
                <strong>Teléfono:</strong> +56 58 220 5500 | 
                <strong>Email:</strong> reservas@muniarica.cl
            </p>
        </div>
    </div>

    <!-- Footer institucional -->
    <div style="padding: 0; text-align: center;">
        <img src="{{ $message->embed(public_path('images/footer-municipalidad.png')) }}" alt="Footer Municipalidad de Arica" style="width: 100%; max-width: 600px; height: auto; display: block;">
    </div>

</body>
</html>
