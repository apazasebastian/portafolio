<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            background: #2563eb;
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px 20px;
        }
        .content p {
            margin: 0 0 15px;
        }
        .button {
            display: inline-block;
            background: #2563eb;
            color: #ffffff;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .button:hover {
            background: #1d4ed8;
        }
        .alert {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .footer {
            background: #f9fafb;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
        }
        .link {
            color: #2563eb;
            word-break: break-all;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Recuperación de Contraseña</h1>
        </div>
        
        <div class="content">
            <p>Hola,</p>
            
            <p>Recibimos una solicitud para restablecer la contraseña de tu cuenta en el Sistema de Reservas Deportivas.</p>
            
            <p>Haz clic en el siguiente botón para crear una nueva contraseña:</p>
            
            <div style="text-align: center;">
                <a href="{{ $url }}" class="button">Restablecer Contraseña</a>
            </div>
            
            <div class="alert">
                <strong> Importante:</strong>
                <ul style="margin: 10px 0 0; padding-left: 20px;">
                    <li>Este enlace expirará en <strong>60 minutos</strong></li>
                    <li>Si no solicitaste este cambio, ignora este correo</li>
                    <li>Tu contraseña actual seguirá siendo válida</li>
                </ul>
            </div>
            
            <p><strong>Si el botón no funciona, copia y pega este enlace en tu navegador:</strong></p>
            <p class="link">{{ $url }}</p>
            
            <p style="margin-top: 30px; font-size: 14px; color: #6b7280;">
                Si no solicitaste restablecer tu contraseña, no es necesario que hagas nada. Tu cuenta está segura.
            </p>
        </div>
        
        <div class="footer">
            <p><strong>Sistema de Reservas Deportivas</strong></p>
            <p>Este es un correo automático, por favor no respondas a este mensaje.</p>
            <p style="margin-top: 10px;">
                © {{ date('Y') }} Todos los derechos reservados
            </p>
        </div>
    </div>
</body>
</html>