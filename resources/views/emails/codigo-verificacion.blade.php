<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Código de Verificación 2FA</title>
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <!--[if mso]>
    <xml>
        <o:OfficeDocumentSettings>
            <o:PixelsPerInch>96</o:PixelsPerInch>
        </o:OfficeDocumentSettings>
    </xml>
    <![endif]-->
</head>
<body style="margin: 0; padding: 0; background-color: #f3f4f6; font-family: 'Segoe UI', system-ui, -apple-system, BlinkMacSystemFont, 'Helvetica Neue', Arial, sans-serif; -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="min-width: 320px;">
        <tr>
            <td align="center" style="padding: 32px 16px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width: 480px; background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08);">
                    {{-- Header --}}
                    <tr>
                        <td style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); border-radius: 16px 16px 0 0; padding: 36px 24px; text-align: center;">
                            <h1 style="margin: 0 0 8px; color: white; font-size: 26px; font-weight: 700; letter-spacing: -0.5px;">Consultorio Jurídico DAE</h1>
                            <p style="margin: 0; color: rgba(255,255,255,0.9); font-size: 14px; font-weight: 500;">Universidad de San Pedro Sula</p>
                        </td>
                    </tr>

                    {{-- Content --}}
                    <tr>
                        <td style="padding: 36px 28px;">
                            <p style="font-size: 16px; color: #374151; margin: 0 0 12px;">Hola,</p>
                            
                            <p style="font-size: 16px; color: #374151; margin: 0 0 20px; line-height: 1.6;">
                                Se ha solicitado acceso a tu cuenta. Utiliza el siguiente código para completar tu inicio de sesión con autenticación de dos factores (2FA):
                            </p>

                            {{-- Código OTP --}}
                            <div style="text-align: center; margin: 32px 0;">
                                <span style="display: inline-block; background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); border-radius: 12px; padding: 18px 36px; box-shadow: 0 8px 24px rgba(30, 58, 138, 0.3);">
                                    <span style="font-family: 'SF Mono', 'Monaco', 'Inconsolata', 'Fira Mono', monospace; font-size: 36px; font-weight: 800; letter-spacing: 8px; color: white; text-shadow: 0 2px 4px rgba(0,0,0,0.15);">
                                        {{ $codigo }}
                                    </span>
                                </span>
                            </div>

                            <p style="font-size: 13px; color: #6b7280; text-align: center; margin: 32px 0 0; line-height: 1.6;">
                                Este código expira en <strong>15 minutos</strong> por seguridad.<br>
                                Si tú no solicitaste este acceso, puedes ignorar este mensaje de forma segura.
                            </p>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background: #f9fafb; border-top: 1px solid #e5e7eb; border-radius: 0 0 16px 16px; padding: 20px 24px; text-align: center;">
                            <p style="margin: 0 0 8px; font-size: 12px; color: #6b7280; font-weight: 600;">Consultorio Jurídico DAE</p>
                            <p style="margin: 0 0 4px; font-size: 11px; color: #9ca3af;">Universidad de San Pedro Sula (USAP)</p>
                            <p style="margin: 0 0 4px; font-size: 11px; color: #9ca3af;">Dirección de Asuntos Estudiantiles</p>
                            <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 16px 0;">
                            <p style="margin: 0; font-size: 10px; color: #d1d5db;">Este es un correo automático del sistema 2FA, por favor no responder.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>