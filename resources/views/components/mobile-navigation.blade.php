{{--
    Componente: mobile-navigation
    Propósito: Barra de navegación inferior fija para dispositivos móviles. Incluye enlaces a Dashboard, Casos, Clientes, Agenda y un menú "Más" con Usuarios, Procuradores y Cerrar Sesión.
    Variables: Auth::user() (usuario autenticado para mostrar opciones según rol)
--}}
<nav class="md:hidden fixed bottom-0 left-0 right-0 z-50 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 shadow-lg" x-data="{ activeTab: window.location.pathname }">
    <div class="flex items-center justify-around h-16 px-2">
        <!-- Dashboard -->
        <a href="/dashboard" 
           class="flex flex-col items-center justify-center px-3 py-1 rounded-lg transition-colors"
           :class="activeTab === '/dashboard' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 hover:text-blue-500'">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h2a1 1 0 001-1v-7m-6 0l2 2"/>
            </svg>
            <span class="text-xs mt-1 font-medium">Inicio</span>
        </a>

        <!-- Casos -->
        <a href="/casos"
           class="flex flex-col items-center justify-center px-3 py-1 rounded-lg transition-colors"
           :class="activeTab.startsWith('/casos') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 hover:text-blue-500'">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            <span class="text-xs mt-1 font-medium">Casos</span>
        </a>

        <!-- Clientes -->
        <a href="/clientes"
           class="flex flex-col items-center justify-center px-3 py-1 rounded-lg transition-colors"
           :class="activeTab.startsWith('/clientes') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 hover:text-blue-500'">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <span class="text-xs mt-1 font-medium">Clientes</span>
        </a>

        <!-- Agenda -->
        <a href="/agenda"
           class="flex flex-col items-center justify-center px-3 py-1 rounded-lg transition-colors"
           :class="activeTab === '/agenda' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 hover:text-blue-500'">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-3 4h6M5 22h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v13a2 2 0 002 2z"/>
            </svg>
            <span class="text-xs mt-1 font-medium">Agenda</span>
        </a>

        <!-- Más (menú expandible) -->
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open"
                    class="flex flex-col items-center justify-center px-3 py-1 rounded-lg transition-colors text-gray-500 dark:text-gray-400 hover:text-blue-500">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                </svg>
                <span class="text-xs mt-1 font-medium">Más</span>
            </button>

            <!-- Menú desplegable "Más" -->
            <div x-show="open" @click.away="open = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="absolute bottom-16 right-0 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 py-2">
                
                @if(Auth::check() && Auth::user()->rol->rol_nombre === 'Director')
                <a href="/usuarios" 
                   class="flex items-center px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="h-5 w-5 mr-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    Usuarios
                </a>
                <a href="/procuradores"
                   class="flex items-center px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="h-5 w-5 mr-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Procuradores
                </a>
                <hr class="my-1 border-gray-200 dark:border-gray-700">
                @endif

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                       class="flex items-center w-full px-4 py-3 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20">
                        <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Cerrar Sesión
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
