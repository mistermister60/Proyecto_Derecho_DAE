@props(['id', 'titulo' => '', 'maxWidth' => 'max-w-lg'])

<div x-data="{ show: false, previousFocus: null }"
     x-id="['modal-{{ $id }}']"
     x-init="$watch('show', val => {
         if (val) {
             previousFocus = document.activeElement;
             document.body.style.overflow = 'hidden';
             $nextTick(() => $refs.closeBtn?.focus());
         } else {
             document.body.style.overflow = '';
             previousFocus?.focus();
         }
     })"
     @open-modal-{{ $id }}.window="show = true"
     @close-modal-{{ $id }}.window="show = false"
     @keydown.escape.window="show = false"
     x-cloak>

    {{-- Backdrop --}}
    <div x-show="show"
         x-transition:enter="transition-opacity duration-200"
         x-transition:leave="transition-opacity duration-150"
         class="fixed inset-0 z-40 bg-black/40"
         @click="show = false"
         aria-hidden="true">
    </div>

    {{-- Modal content --}}
    <div x-show="show"
         x-transition:enter="transition-all duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:leave="transition-all duration-150"
         x-transition:leave-end="opacity-0 scale-95"
         role="dialog"
         aria-modal="true"
         @if ($titulo) aria-labelledby="modal-title-{{ $id }}" @endif
         class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="w-full {{ $maxWidth }} rounded-xl shadow-xl bg-white border border-gray-200"
             @click.stop
             x-trap.noscroll="show">
            {{-- Header --}}
            @if ($titulo)
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h3 id="modal-title-{{ $id }}" class="text-lg font-semibold text-gray-900">{{ $titulo }}</h3>
                <button @click="show = false"
                        x-ref="closeBtn"
                        type="button"
                        aria-label="Cerrar"
                        class="p-1 rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
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
