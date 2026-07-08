{{--
    Componente: input-error
    Propósito: Muestra los errores de validación de un campo del formulario. Renderiza una lista con role="alert".
    Props: $messages (array de mensajes de error)
--}}
@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'text-sm text-red-600 space-y-1']) }} role="alert">
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
