{{--
    Componente: input-label
    Propósito: Etiqueta de formulario con estilo estandarizado.
    Props: $value (texto de la etiqueta)
--}}
@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-gray-700']) }}>
    {{ $value ?? $slot }}
</label>
