{{--
    Componente: password-input
    Propósito: Campo de contraseña accesible con alternador de visibilidad (mostrar/ocultar)
               implementado con Alpine.js. El botón con icono de ojo cambia el tipo del input
               entre "password" y "text" sin recargar la página.
    Props:
      - name (string, requerido): nombre del campo enviado en el formulario.
      - id (string|null): identificador del input. Por defecto usa el valor de name.
      - label (string|null): texto de etiqueta opcional. Si se omite, no se renderiza <label>.
      - required (bool): indica si el campo es obligatorio.
      - autocomplete (string|null): valor del atributo autocomplete.
      - placeholder (string|null): texto de ayuda del input.
      - $attributes: atributos extra (class, style, minlength, disabled, etc.).
--}}
@props([
    'name',
    'id' => null,
    'label' => null,
    'required' => false,
    'autocomplete' => null,
    'placeholder' => null,
])

@php
    $inputId = $id ?? $name;
    $baseClass = 'w-full rounded-lg border border-gray-300 px-3 py-2.5 pr-11 text-sm text-gray-900 bg-white outline-none transition-colors focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20';
    $inputClass = $attributes->has('class') ? $attributes->get('class') . ' pr-11' : $baseClass;
    $extraAttributes = $attributes->except('class');
@endphp

<div x-data="{ show: false }">
    @if ($label)
        <label for="{{ $inputId }}" class="block font-medium text-sm text-gray-700 mb-1.5">{{ $label }}</label>
    @endif

    <div class="relative">
        <input
            type="password"
            :type="show ? 'text' : 'password'"
            name="{{ $name }}"
            id="{{ $inputId }}"
            @if ($required) required @endif
            @if ($autocomplete) autocomplete="{{ $autocomplete }}" @endif
            @if ($placeholder) placeholder="{{ $placeholder }}" @endif
            {{ $extraAttributes->merge(['class' => $inputClass]) }}
        >

        <button
            type="button"
            x-on:click="show = !show"
            x-bind:aria-label="show ? 'Ocultar contraseña' : 'Mostrar contraseña'"
            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-600 rounded"
        >
            {{-- Ojo abierto: visible cuando la contraseña está oculta --}}
            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
            </svg>
            {{-- Ojo tachado: visible cuando la contraseña está mostrada --}}
            <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" style="display: none;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.243 4.243-4.243-4.243" />
            </svg>
        </button>
    </div>
</div>
