@component('mail::layout')
{{-- Header --}}
@slot('header')
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); border-radius: 16px 16px 0 0; padding: 40px 30px; text-align: center;">
    <tr>
        <td style="text-align: center;">
            <h1 style="margin: 0; color: white; font-size: 28px; font-weight: 700; letter-spacing: -0.5px;">Consultorio Jurídico DAE</h1>
            <p style="margin: 12px 0 0; color: rgba(255,255,255,0.9); font-size: 16px; font-weight: 500;">Restablecimiento de Contraseña</p>
        </td>
    </tr>
</table>
@endslot

{{-- Content --}}
@slot('content')
<div style="padding: 40px 30px;">
    <p style="font-size: 16px; color: #374151; margin: 0 0 16px;">Hola <strong>{{ $nombre }}</strong>,</p>
    
    <p style="font-size: 16px; color: #374151; margin: 0 0 16px;">
        Recibimos una solicitud para restablecer la contraseña de tu cuenta en el Consultorio Jurídico DAE. 
        Si no fuiste tú, puedes ignorar este correo.
    </p>

    <p style="font-size: 16px; color: #374151; margin: 0 0 24px;">
        Para crear una nueva contraseña, haz clic en el siguiente botón (válido por <strong>60 minutos</strong>):
    </p>

    {{-- Button --}}
    <table role="presentation" cellspacing="0" cellpadding="0" style="margin: 0 auto 30px;">
        <tr>
            <td style="border-radius: 10px; background: #1e3a8a; box-shadow: 0 4px 14px rgba(30, 58, 138, 0.3);">
                <a href="{{ url('/restablecer-contrasena/' . $token . '?email=' . urlencode($email)) }}" 
                   style="display: inline-block; padding: 16px 36px; color: white; text-decoration: none; font-weight: 700; font-size: 16px; border-radius: 10px; letter-spacing: 0.3px;">
                    Restablecer Mi Contraseña
                </a>
            </td>
        </tr>
    </table>

    <p style="font-size: 14px; color: #6b7280; margin: 0 0 16px; text-align: center;">
        Si el botón no funciona, copia y pega este enlace en tu navegador:
    </p>
    <p style="font-size: 13px; color: #3b82f6; word-break: break-all; background: #f3f4f6; padding: 14px; border-radius: 8px; margin: 0 0 24px; text-align: center; font-family: monospace;">
        {{ url('/restablecer-contrasena/' . $token) }}
    </p>

    <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 24px 0;">

    <div style="background: #fef3c7; border: 1px solid #fde68a; border-radius: 10px; padding: 16px; margin: 0 0 24px;">
        <p style="margin: 0 0 8px; font-size: 13px; color: #92400e; font-weight: 600; display: flex; align-items: center; gap: 8px;">
            <span style="background: #f59e0b; color: white; width: 20px; height: 20px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 11px;">⚠</span>
            Por seguridad
        </p>
        <ul style="margin: 0; padding-left: 20px; color: #92400e; font-size: 13px; line-height: 2;">
            <li>Este enlace expira en <strong>60 minutos</strong></li>
            <li>Solo puede usarse <strong>una vez</strong></li>
            <li>Tu nueva contraseña debe tener: <strong>8+ caracteres, mayúscula, minúscula, número y símbolo</strong></li>
        </ul>
    </div>

    <p style="font-size: 14px; color: #6b7280; margin: 24px 0 0;">
        Si tienes problemas, contacta al <strong>Director del Consultorio Jurídico</strong>.
    </p>
</div>
@endslot

{{-- Footer --}}
@slot('footer')
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background: #f9fafb; border-top: 1px solid #e5e7eb; border-radius: 0 0 16px 16px;">
    <tr>
        <td style="padding: 24px 20px; text-align: center;">
            <p style="margin: 0 0 8px; font-size: 13px; color: #6b7280; font-weight: 600;">Consultorio Jurídico DAE</p>
            <p style="margin: 0 0 8px; font-size: 12px; color: #9ca3af;">Universidad de San Pedro Sula (USAP)</p>
            <p style="margin: 0 0 8px; font-size: 12px; color: #9ca3af;">Dirección de Asuntos Estudiantiles</p>
            <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 16px 0;">
            <p style="margin: 0; font-size: 11px; color: #d1d5db;">Este es un correo automático del sistema, por favor no responder.</p>
        </td>
    </tr>
</table>
@endslot
@endcomponent