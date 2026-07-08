{{--
    Componente: modal
    Propósito: Ventana modal con Alpine.js. Incluye backdrop semitransparente, encabezado con título, botón de cierre y cuerpo con slot. Se activa con eventos window personalizados.
    Props: $id (identificador único), $titulo (texto del encabezado), $maxWidth (clase de ancho máximo), $slot (contenido del cuerpo)
--}}
@props(['id', 'titulo' => '', 'maxWidth' => 'max-w-lg'])

<div x-data="{ show: false }"
     x-id="['modal-{{ $id }}']"
     x-init="$watch('show', val => document.body.style.overflow = val ? 'hidden' : '')"
     @open-modal-{{ $id }}.window="show = true"
     @close-modal-{{ $id }}.window="show = false"
     x-cloak>

    {{-- Backdrop --}}
    <div x-show="show"
         x-transition:enter="transition-opacity duration-200"
         x-transition:leave="transition-opacity duration-150"
         class="fixed inset-0 z-40"
         style="background: rgba(0,0,0,0.4);"
         @click="show = false">
    </div>

    {{-- Modal content --}}
    <div x-show="show"
         x-transition:enter="transition-all duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:leave="transition-all duration-150"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="w-full {{ $maxWidth }} rounded-xl shadow-xl"
             style="background: #FFFFFF; border: 1px solid #E5E7EB;"
             @click.stop>
            {{-- Header --}}
            @if ($titulo)
            <div class="flex items-center justify-between px-6 py-4" style="border-bottom: 1px solid #E5E7EB;">
                <h3 class="text-lg font-semibold" style="color: #111827;">{{ $titulo }}</h3>
                <button @click="show = false" class="p-1 rounded-lg transition-colors" style="color: #9CA3AF;" onmouseover="this.style.background='#F3F4F6';" onmouseout="this.style.background='transparent';">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>
            @endif

            {{-- Body --}}
            <div class="px-6 py-4">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
