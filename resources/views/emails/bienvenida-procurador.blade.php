@extends('emails.layout')

@section('header')
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); padding: 40px 20px; text-align: center;">
    <tr>
        <td>
            <div style="display: inline-flex; align-items: center; justify-content: center; width: 80px; height: 80px; background: rgba(255,255,255,0.15); border-radius: 50%; margin-bottom: 16px;">
                <svg width="40" height="40" viewBox="0 0 512 512" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <g transform="translate(256,260)" fill="white" stroke="white" stroke-width="12">
                        <polygon points="0,-80 -120,80 120,80" fill="none" stroke="white" stroke-width="14" stroke-linejoin="round"/>
                        <line x1="-140" y1="-20" x2="140" y2="-20" stroke="white" stroke-width="14" stroke-linecap="round"/>
                        <line x1="0" y1="-80" x2="0" y2="-20" stroke="white" stroke-width="14" stroke-linecap="round"/>
                        <line x1="-140" y1="-20" x2="-140" y2="30" stroke="white" stroke-width="8" stroke-linecap="round"/>
                        <ellipse cx="-140" cy="40" rx="50" ry="14" fill="white"/>
                        <line x1="140" y1="-20" x2="140" y2="30" stroke="white" stroke-width="8" stroke-linecap="round"/>
                        <ellipse cx="140" cy="40" rx="50" ry="14" fill="white"/>
                    </g>
                </svg>
            </div>
            <h1 style="margin: 0; color: white; font-size: 26px; font-weight: 700; font-family: 'Segoe UI', system-ui, sans-serif;">Consultorio Jurídico DAE</h1>
            <p style="margin: 8px 0 0; color: rgba(255,255,255,0.9); font-size: 14px;">Universidad de San Pedro Sula</p>
        </td>
    </tr>
</table>
@endsection

@section('content')
<div style="font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; color: #1f2937; line-height: 1.6;">

    <p style="font-size: 16px; margin: 0 0 16px;">Hola <strong style="color: #1e3a8a;">{{ $nombre }}</strong>,</p>

    <p style="font-size: 16px; margin: 0 0 24px;">
        ¡Bienvenido al <strong>Sistema de Gestión del Consultorio Jurídico DAE</strong>! 
        Tu cuenta ha sido creada exitosamente por el Director del Consultorio.
    </p>

    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; margin: 24px 0;">
        <h2 style="margin: 0 0 20px; font-size: 18px; color: #1e3a8a; font-weight: 700; display: flex; align-items: center; gap: 8px;">
            <span style="background: #1e3a8a; color: white; width: 28px; height: 28px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; font-size: 14px;">🔐</span>
            Tus credenciales de acceso
        </h2>

        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="font-size: 15px;">
            <tr>
                <td style="padding: 12px 0; border-bottom: 1px solid #e2e8f0; color: #6b7280; width: 140px;">Correo institucional</td>
                <td style="padding: 12px 0; border-bottom: 1px solid #e2e8f0; font-weight: 600; color: #1f2937;">{{ $email }}</td>
            </tr>
            <tr>
                <td style="padding: 12px 0; border-bottom: 1px solid #e2e8f0; color: #6b7280;">Contraseña temporal</td>
                <td style="padding: 12px 0; border-bottom: 1px solid #e2e8f0;">
                    <code style="background: #1e3a8a; color: #fcd34d; padding: 6px 12px; border-radius: 6px; font-size: 15px; font-family: 'Monaco', 'Menlo', monospace; font-weight: 700; letter-spacing: 0.5px;">{{ $tempPassword }}</code>
                </td>
            </tr>
            <tr>
                <td style="padding: 12px 0; color: #6b7280;">Enlace de acceso</td>
                <td style="padding: 12px 0;">
                    <a href="{{ $loginUrl }}" style="display: inline-block; background: #1e3a8a; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px; box-shadow: 0 2px 4px rgba(30,58,94,0.3);">
                        🚀 Ir al Consultorio Jurídico
                    </a>
                </td>
            </tr>
        </table>
    </div>

    <div style="background: #fef3c7; border: 1px solid #fcd34d; border-radius: 12px; padding: 20px; margin: 24px 0;">
        <h3 style="margin: 0 0 16px; font-size: 16px; color: #92400e; font-weight: 700; display: flex; align-items: center; gap: 8px;">
            <span style="background: #f59e0b; color: white; width: 24px; height: 24px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 12px;">⚠</span>
            Pasos obligatorios para tu primer ingreso
        </h3>
        <ol style="margin: 0; padding-left: 20px; color: #78350f; font-size: 14px; line-height: 2;">
            <li style="margin-bottom: 8px;">Haz clic en <strong>"Ir al Consultorio Jurídico"</strong> arriba o visita: <code>{{ $loginUrl }}</code></li>
            <li style="margin-bottom: 8px;">Ingresa tu <strong>correo</strong> y la <strong>contraseña temporal</strong> (copia y pega para evitar errores)</li>
            <li style="margin-bottom: 8px;">Recibirás un <strong>código OTP de 6 dígitos por correo</strong> (autenticación de dos factores)</li>
            <li style="margin-bottom: 8px;">Ingresa el código OTP (expira en 15 minutos)</li>
            <li><strong>Cambia tu contraseña obligatoriamente</strong> — la temporal expira en el primer uso</li>
        </ol>
    </div>

    <div style="background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 12px; padding: 20px; margin: 24px 0;">
        <h3 style="margin: 0 0 12px; font-size: 15px; color: #065f46; font-weight: 700; display: flex; align-items: center; gap: 8px;">
            <span style="background: #10b981; color: white; width: 24px; height: 24px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 12px;">✓</span>
            Requisitos de tu nueva contraseña
        </h3>
        <ul style="margin: 0; padding-left: 20px; color: #065f46; font-size: 14px; line-height: 2;">
            <li>Mínimo <strong>8 caracteres</strong></li>
            <li>Al menos <strong>una mayúscula</strong> y <strong>una minúscula</strong></li>
            <li>Al menos <strong>un número</strong> (0-9)</li>
            <li>Al menos <strong>un símbolo</strong> (!@#$%^&*...)</li>
            <li>No puede ser igual a la contraseña temporal</li>
        </ul>
    </div>

    <p style="font-size: 14px; color: #6b7280; margin: 24px 0 0;">
        <strong>Importante:</strong> La contraseña temporal <strong>solo funciona una vez</strong>. 
        Guarda tu nueva contraseña en un lugar seguro (gestor de contraseñas recomendado).
    </p>
</div>
@endsection

@section('footer')
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background: #f9fafb; border-top: 1px solid #e5e7eb;">
    <tr>
        <td style="padding: 24px 20px; text-align: center;">
            <p style="margin: 0 0 8px; font-size: 13px; color: #6b7280; font-weight: 600;">Consultorio Jurídico DAE</p>
            <p style="margin: 0 0 8px; font-size: 12px; color: #9ca3af;">Universidad de San Pedro Sula (USAP)</p>
            <p style="margin: 0 0 8px; font-size: 12px; color: #9ca3af;">Dirección de Asuntos Estudiantiles</p>
            <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 16px 0;">
            <p style="margin: 0; font-size: 11px; color: #d1d5db;">Este es un correo automático del sistema, por favor no responder.<br>Si no solicitaste esta cuenta, contacta al Director del Consultorio Jurídico.</p>
        </td>
    </tr>
</table>
@endsection