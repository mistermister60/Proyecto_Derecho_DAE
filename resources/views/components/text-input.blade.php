{{--
    Componente: text-input
    Propósito: Campo de entrada de texto con estilo estandarizado. Soporta el estado deshabilitado.
    Props: $disabled (bool), $attributes (type, name, value, etc.)
--}}
@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm']) }}>
