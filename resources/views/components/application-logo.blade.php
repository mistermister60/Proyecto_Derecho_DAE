{{--
    Componente: application-logo
    Propósito: Logo de la aplicación con la balanza de la justicia. Se usa en layouts públicos (login) y navegación principal.
    Props: $attributes (clases HTML adicionales, p.ej. w-20 h-20 fill-current text-gray-500)
--}}
<svg viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" {{ $attributes }}>
    {{-- Base triangular de la balanza --}}
    <polygon points="24,4 4,40 44,40" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linejoin="round"/>
    {{-- Brazo horizontal --}}
    <line x1="2" y1="16" x2="46" y2="16" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
    {{-- Eje central --}}
    <line x1="24" y1="4" x2="24" y2="16" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
    {{-- Cadena izquierda --}}
    <line x1="2" y1="16" x2="2" y2="30" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
    {{-- Plato izquierdo --}}
    <ellipse cx="2" cy="34" rx="14" ry="4" fill="currentColor" opacity="0.9"/>
    {{-- Cadena derecha --}}
    <line x1="46" y1="16" x2="46" y2="30" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
    {{-- Plato derecho --}}
    <ellipse cx="46" cy="34" rx="14" ry="4" fill="currentColor" opacity="0.9"/>
    {{-- Libro/ley en la base --}}
    <rect x="18" y="40" width="12" height="5" rx="1" fill="currentColor" opacity="0.35"/>
</svg>
