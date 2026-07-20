<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña - Consultorio Jurídico DAE</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f3f4f6;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width: 600px; margin: 40px auto; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
        <!-- Header -->
        <tr>
            <td style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); padding: 40px 30px; text-align: center;">
                <h1 style="margin: 0; color: white; font-size: 28px; font-weight: bold;">Consultorio Jurídico DAE</h1>
                <p style="margin: 10px 0 0; color: rgba(255,255,255,0.9); font-size: 16px;">Restablecimiento de Contraseña</p>
            </td>
        </tr>

        <!-- Content -->
        <tr>
            <td style="padding: 40px 30px;">
                <p style="font-size: 16px; color: #374151; margin: 0 0 16px;">Hola <strong>{{ $nombre }}</strong>,</p>
                
                <p style="font-size: 16px; color: #374151; margin: 0 0 16px;">Recibimos una solicitud para restablecer la contraseña de tu cuenta en el Consultorio Jurídico DAE. Si no fuiste tú, puedes ignorar este correo.</p>

                <p style="font-size: 16px; color: #374151; margin: 0 0 24px;">Para crear una nueva contraseña, haz clic en el siguiente botón (válido por 60 minutos):</p>

                <!-- Button -->
                <table role="presentation" cellspacing="0" cellpadding="0" style="margin: 0 auto 30px;">
                    <tr>
                        <td style="border-radius: 8px; background: #1e3a8a;">
                            <a href="{{ url('/restablecer-contrasena/' . $token . '?email=' . urlencode($email)) }}" 
                               style="display: inline-block; padding: 14px 32px; color: white; text-decoration: none; font-weight: bold; font-size: 16px; border-radius: 8px;">
                                Restablecer Mi Contraseña
                            </a>
                        </td>
                    </tr>
                </table>

                <p style="font-size: 14px; color: #6b7280; margin: 0 0 16px;">Si el botón no funciona, copia y pega este enlace en tu navegador:</p>
                <p style="font-size: 13px; color: #3b82f6; word-break: break-all; background: #f3f4f6; padding: 12px; border-radius: 8px; margin: 0 0 24px;">{{ url('/restablecer-contrasena/' . $token) }}</p>

                <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 24px 0;">

                <p style="font-size: 13px; color: #9ca3af; margin: 0;">Por seguridad, este enlace expira en <strong>60 minutos</strong> y solo puede usarse una vez.</p>
            </td>
        </tr>

        <!-- Footer -->
        <tr>
            <td style="background: #f9fafb; padding: 24px 30px; text-align: center; border-top: 1px solid #e5e7eb;">
                <p style="margin: 0 0 8px; font-size: 13px; color: #9ca3af;">Universidad de San Pedro Sula (USAP)</p>
                <p style="margin: 0; font-size: 12px; color: #9ca3af;">Dirección de Asuntos Estudiantiles - Consultorio Jurídico</p>
                <p style="margin: 16px 0 0; font-size: 11px; color: #d1d5db;">Este es un correo automático, por favor no responder.</p>
            </td>
        </tr>
    </table>
</body>
</html>