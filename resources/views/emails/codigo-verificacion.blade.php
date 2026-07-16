<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Código de Verificación</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; margin: 0;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
        <div style="background-color: #1e3a8a; padding: 30px; text-align: center; color: #ffffff;">
            <h2 style="margin: 0;">Consultorio Jurídico</h2>
            <p style="margin: 5px 0 0 0; font-size: 14px; opacity: 0.8;">Universidad de San Pedro Sula</p>
        </div>
        <div style="padding: 30px; color: #333333;">
            <p>Hola,</p>
            <p>Se ha solicitado un acceso de seguridad para tu cuenta. Por favor, utiliza el siguiente código para completar tu inicio de sesión (2FA):</p>
            
            <div style="text-align: center; margin: 30px 0;">
                <span style="display: inline-block; background-color: #f3f4f6; border: 2px dashed #1e3a8a; color: #1e3a8a; font-size: 32px; font-weight: bold; letter-spacing: 5px; padding: 15px 30px; border-radius: 6px;">
                    {{ $codigo }}
                </span>
            </div>

            <p style="font-size: 12px; color: #6b7280; text-align: center; margin-top: 40px;">
                Este código expira pronto por seguridad. Si tú no solicitaste este acceso, puedes ignorar este mensaje de forma segura.
            </p>
        </div>
        <div style="background-color: #f9fafb; padding: 15px; text-align: center; font-size: 11px; color: #9ca3af; border-top: 1px solid #e5e7eb;">
            Desarrollado por estudiantes de la clase de Desarrollo de Aplicaciones — USAP © 2026
        </div>
    </div>
</body>
</html>