<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserva Cancelada</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f3f4f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f3f4f6; padding: 20px 0;">
        <tr>
            <td align="center">
                <table border="0" cellpadding="0" cellspacing="0" width="600" style="background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    
                    <tr>
                        <td align="center" style="background: linear-gradient(135deg, #4b5563 0%, #1f2937 100%); padding: 40px 20px;">
                            <div style="width: 60px; height: 60px; background-color: rgba(255,255,255,0.1); border-radius: 50%; line-height: 60px; font-size: 30px; color: white; margin-bottom: 15px;">
                                ✕
                            </div>
                            <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: bold;">Reserva Cancelada</h1>
                            <p style="color: #e5e7eb; margin: 5px 0 0; font-size: 16px;">Confirmación de anulación</p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 40px 30px;">
                            <p style="color: #374151; font-size: 16px; margin-bottom: 20px;">
                                Estimado/a <strong>{{ $reserva->representante_nombre }}</strong>,
                            </p>
                            
                            <p style="color: #4b5563; font-size: 15px; line-height: 1.6; margin-bottom: 25px;">
                                Te informamos que la reserva solicitada ha sido cancelada exitosamente. A continuación encontrarás los detalles:
                            </p>

                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                            
                                            <tr>
                                                <td width="35%" style="padding: 8px 0; color: #6b7280; font-weight: bold; font-size: 14px; vertical-align: top;">
                                                    🏟️ Recinto:
                                                </td>
                                                <td width="65%" style="padding: 8px 0; color: #111827; font-weight: 500; font-size: 14px;">
                                                    {{ $reserva->recinto->nombre }}
                                                </td>
                                            </tr>
                                            <tr><td colspan="2" style="border-bottom: 1px solid #e5e7eb;"></td></tr>

                                            <tr>
                                                <td style="padding: 12px 0 8px; color: #6b7280; font-weight: bold; font-size: 14px; vertical-align: top;">
                                                    📅 Fecha del Evento:
                                                </td>
                                                <td style="padding: 12px 0 8px; color: #111827; font-weight: 500; font-size: 14px;">
                                                    {{ $reserva->fecha_reserva->format('d/m/Y') }}
                                                </td>
                                            </tr>
                                            <tr><td colspan="2" style="border-bottom: 1px solid #e5e7eb;"></td></tr>

                                            <tr>
                                                <td style="padding: 12px 0 8px; color: #6b7280; font-weight: bold; font-size: 14px; vertical-align: top;">
                                                    🕐 Horario:
                                                </td>
                                                <td style="padding: 12px 0 8px; color: #111827; font-weight: 500; font-size: 14px;">
                                                    {{ \Carbon\Carbon::parse($reserva->hora_inicio)->format('H:i') }} - 
                                                    {{ \Carbon\Carbon::parse($reserva->hora_fin)->format('H:i') }}
                                                </td>
                                            </tr>
                                            <tr><td colspan="2" style="border-bottom: 1px solid #e5e7eb;"></td></tr>

                                            <tr>
                                                <td style="padding: 12px 0 8px; color: #6b7280; font-weight: bold; font-size: 14px; vertical-align: top;">
                                                    🏢 Organización:
                                                </td>
                                                <td style="padding: 12px 0 8px; color: #111827; font-weight: 500; font-size: 14px;">
                                                    {{ $reserva->nombre_organizacion }}
                                                </td>
                                            </tr>
                                            <tr><td colspan="2" style="border-bottom: 1px solid #e5e7eb;"></td></tr>

                                            <tr>
                                                <td style="padding: 12px 0 0; color: #ef4444; font-weight: bold; font-size: 14px; vertical-align: top;">
                                                    📆 Cancelada el:
                                                </td>
                                                <td style="padding: 12px 0 0; color: #ef4444; font-weight: 500; font-size: 14px;">
                                                    {{ $reserva->fecha_cancelacion ? $reserva->fecha_cancelacion->timezone('America/Santiago')->format('d/m/Y H:i') : now()->timezone('America/Santiago')->format('d/m/Y H:i') }}
                                                </td>
                                            </tr>

                                        </table>
                                    </td>
                                </tr>
                            </table>

                            @if($reserva->motivo_cancelacion)
                            <div style="margin-top: 25px; background-color: #fffbeb; border-left: 4px solid #f59e0b; padding: 15px; border-radius: 4px;">
                                <p style="margin: 0; font-size: 14px; color: #92400e; font-weight: bold;">📝 Motivo de la Cancelación:</p>
                                <p style="margin: 5px 0 0; font-size: 14px; color: #b45309;">{{ $reserva->motivo_cancelacion }}</p>
                            </div>
                            @endif

                            <div style="margin-top: 20px; background-color: #eff6ff; border-radius: 6px; padding: 15px;">
                                <p style="margin: 0; font-size: 13px; color: #1e40af; text-align: center;">
                                    ℹ️ El horario ha quedado liberado para otras reservas.
                                </p>
                            </div>

                        </td>
                    </tr>

                    <tr>
                        <td style="background-color: #f9fafb; padding: 20px; text-align: center; border-top: 1px solid #e5e7eb;">
                            <p style="color: #6b7280; font-size: 12px; margin: 0;">
                                &copy; {{ date('Y') }} Municipalidad de Arica<br>
                                Departamento de Deportes y Recreación
                            </p>
                        </td>
                    </tr>
                </table>
                
                <table border="0" cellpadding="0" cellspacing="0" height="40" width="100%">
                    <tr><td></td></tr>
                </table>
            </td>
        </tr>
    </table>

</body>
</html>