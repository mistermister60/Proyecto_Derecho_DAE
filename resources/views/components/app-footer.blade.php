{{--
    Componente: app-footer
    Propósito: Pie de página del sistema autenticado. Muestra créditos académicos del proyecto
    y un botón para instalar la PWA en escritorio (aparece cuando Chrome dispara beforeinstallprompt).
    Variables: ninguna (usa Auth::user() directamente)
--}}

<footer class="mt-auto border-t border-gray-200 bg-white px-4 py-3 print:hidden">
    <div class="flex flex-col sm:flex-row items-center justify-between gap-2 text-xs">
        {{-- Créditos académicos --}}
        <div class="flex items-center gap-2 text-gray-500">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-7.998 12.078 12.078 0 01.665-6.479L12 14z M12 14l9-5-9-5-9 5 9 5z"/>
            </svg>
            <span>
                Desarrollado por estudiantes de la clase de
                <strong class="text-gray-600">Desarrollo de Aplicaciones</strong>
                — Universidad San Pedro Sula &copy; {{ date('Y') }}
            </span>
        </div>

        {{-- Botón instalar PWA (oculto por defecto, se muestra vía JS) --}}
        <button id="install-pwa-button"
                class="hidden items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium text-white transition-colors hover:opacity-90"
                style="background: #2563EB;">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
            </svg>
            <span>Instalar aplicación</span>
        </button>
    </div>
</footer>
