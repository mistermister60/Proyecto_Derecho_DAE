{{--
    Componente: auth-session-status
    Propósito: Muestra un mensaje de estado de sesión (ej. éxito al enviar enlace de restablecimiento de contraseña).
    Props: $status (texto del mensaje de estado)
--}}
@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'font-medium text-sm text-green-600']) }}>
        {{ $status }}
    </div>
@endif
