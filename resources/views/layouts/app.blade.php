<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#6777ef">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Procurador Legal">
    <link rel="apple-touch-icon" href="/logo.png">
    <link rel="manifest" href="/manifest.json">

    <title>@yield('title', 'Consultorio Jurídico - USAP') | Consultorio Jurídico</title>

    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @PwaHead
</head>
<body x-data="{ sidebarOpen: false }">

<div class="flex h-screen w-full overflow-hidden">
    {{-- ==================== SIDEBAR ==================== --}}
    {{-- Mobile overlay --}}
    <div x-show="sidebarOpen" x-cloak
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-black/50 z-40 md:hidden">
    </div>

    <aside class="flex flex-col shrink-0 h-full transition-transform duration-300 ease-in-out
                  max-md:fixed max-md:inset-y-0 max-md:left-0 max-md:z-50 max-md:w-60
                  md:relative md:translate-x-0"
           :class="sidebarOpen ? 'max-md:translate-x-0' : 'max-md:-translate-x-full'"
           style="width: 240px; background: #1E3A5F; border-right: 1px solid rgba(255,255,255,0.08);">

        {{-- Logo --}}
        <div class="flex items-center gap-3 px-5 py-5" style="border-bottom: 1px solid rgba(255,255,255,0.1);">
            <div class="flex items-center justify-center rounded-lg shrink-0" style="width: 36px; height: 36px; background: rgba(37,99,235,0.9);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M12 20V10"/>
                    <path d="M18 20V4"/>
                    <path d="M6 20v-4"/>
                </svg>
            </div>
            <div>
                <div style="color: #FFFFFF; font-size: 12px; font-weight: 600; line-height: 1.2;">
                    Consultorio Jurídico
                </div>
                <div style="color: rgba(255,255,255,0.55); font-size: 10.5px; line-height: 1.4;">
                    Univ. de San Pedro Sula
                </div>
            </div>
        </div>

        {{-- Navegación --}}
<nav class="flex-1 px-3 py-4 space-y-1">
            @php
                $current = request()->route()->getName() ?? 'dashboard';
                $rolUsuario = strtolower(Auth::user()->rol?->rol_nombre ?? ''); // Usado en $navItems abajo
                $esDirector = \App\Enums\RolEnum::equals(Auth::user()->rol?->rol_nombre, \App\Enums\RolEnum::DIRECTOR);

                // 1. Iniciamos con los elementos base que ve el procurador (y todos)
                $navItems = [
                    ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard'],
                    ['route' => 'casos.index', 'label' => 'Casos', 'icon' => 'casos'],
                ];

                // 2. Si NO es procurador, inyectamos los catálogos administrativos
                if ($rolUsuario !== 'procurador') {
                    $navItems[] = ['route' => 'clientes.index', 'label' => 'Clientes', 'icon' => 'clientes'];
                    $navItems[] = ['route' => 'demandados.index', 'label' => 'Demandados', 'icon' => 'demandados'];
                    $navItems[] = ['route' => 'procuradores.index', 'label' => 'Procuradores', 'icon' => 'procuradores'];
                }

                // 3. Audiencias lo ven todos (el procurador solo verá las suyas desde el controlador)
                $navItems[] = ['route' => 'agenda.index', 'label' => 'Audiencias', 'icon' => 'agenda'];
            @endphp

            @foreach ($navItems as $item)
                @php
                    $isActive = str_starts_with($current, explode('.', $item['route'])[0]);
                @endphp
                <a href="{{ route($item['route']) }}"
                   @click="sidebarOpen = false"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all duration-150"
                   style="{{ $isActive ? 'background: rgba(37,99,235,0.2); color: #FFFFFF;' : 'color: rgba(255,255,255,0.65); hover:background: rgba(255,255,255,0.08);' }}"
                   onmouseover="this.style.background='{{ $isActive ? 'rgba(37,99,235,0.2)' : 'rgba(255,255,255,0.08)' }}'; this.style.color='#FFFFFF';"
                   onmouseout="this.style.background='{{ $isActive ? 'rgba(37,99,235,0.2)' : 'transparent' }}'; this.style.color='{{ $isActive ? '#FFFFFF' : 'rgba(255,255,255,0.65)' }}';">
                    @if ($item['icon'] === 'dashboard')
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                            <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
                        </svg>
                    @elseif ($item['icon'] === 'casos')
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
                        </svg>
                    @elseif ($item['icon'] === 'clientes')
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    @elseif ($item['icon'] === 'demandados')
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M20 21v-2a4 4 0 0 0-3-3.87"/><path d="M4 21v-2a4 4 0 0 1 3-3.87"/><circle cx="12" cy="7" r="4"/>
                        </svg>
                    @elseif ($item['icon'] === 'procuradores')
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M12 20h9"/><path d="M12 4h9"/><path d="M4 12h16"/><path d="M4 6h.01"/><path d="M4 18h.01"/><path d="M4 12h.01"/>
                        </svg>    
                    @elseif ($item['icon'] === 'agenda')
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                            <path d="M8 14h.01"/><path d="M12 14h.01"/><path d="M16 14h.01"/>
                            <path d="M8 18h.01"/><path d="M12 18h.01"/><path d="M16 18h.01"/>
                        </svg>
                    @endif
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

{{-- Nuevo caso button (Oculto para el procurador ya que él no crea casos) --}}
        @if ($esDirector)
            <div class="px-3 pb-4">
                <a href="{{ route('casos.create') }}"
                   @click="sidebarOpen = false"
                   class="flex items-center justify-center gap-2 w-full py-2.5 rounded-lg text-sm font-medium transition-all duration-150"
                   style="background: #2563EB; color: white;"
                   onmouseover="this.style.background='#1d4ed8';"
                   onmouseout="this.style.background='#2563EB';">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Nuevo caso
                </a>
            </div>
        @endif
    </aside>

    {{-- ==================== CONTENIDO PRINCIPAL ==================== --}}
    <div class="flex flex-col flex-1 h-full overflow-hidden">

        {{-- Topbar --}}
        <header class="flex items-center justify-between shrink-0 px-4 py-3 md:px-6 md:py-3" style="background: #FFFFFF; border-bottom: 1px solid #E5E7EB;">
            <div class="flex items-center gap-3">
                {{-- Hamburger (mobile only) --}}
                <button @click="sidebarOpen = !sidebarOpen" class="md:hidden p-2 -ml-2 rounded-lg transition-colors" style="color: #6B7280;" onmouseover="this.style.background='#F3F4F6';" onmouseout="this.style.background='transparent';" aria-label="Abrir menú">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                </button>
                <h1 class="text-base md:text-lg font-semibold" style="color: #111827;">@yield('title', 'Dashboard')</h1>
            </div>

            <div class="flex items-center gap-2 md:gap-4">
                {{-- Buscador (hidden on mobile) --}}
                <div class="relative hidden sm:block" x-data="{ search: '' }">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#9CA3AF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input type="text" x-model="search" placeholder="Buscar..."
                           class="pl-9 pr-3 py-1.5 rounded-lg text-sm outline-none transition-all duration-150"
                           style="background: #F7F8FA; border: 1px solid #E5E7EB; color: #111827; width: 220px;"
                           onfocus="this.style.borderColor='#2563EB'; this.style.width='260px';"
                           onblur="this.style.borderColor='#E5E7EB'; this.style.width='220px';">
                </div>

                {{-- Notificaciones --}}
                <div class="relative" x-data="{ openNotif: false }">
                    <button @click="openNotif = !openNotif; $event.stopPropagation()" class="relative p-2 rounded-lg transition-colors duration-150" style="color: #6B7280;" onmouseover="this.style.background='#F3F4F6';" onmouseout="this.style.background='transparent';">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                        </svg>
                        @if(isset($notificacionesPendientes) && $notificacionesPendientes > 0)
                            <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full" style="background: #EF4444;"></span>
                        @endif
                    </button>

                    {{-- Dropdown --}}
                    <div x-show="openNotif" x-cloak x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                         @click.outside="openNotif = false" @keydown.escape.window="openNotif = false"
                         class="absolute top-12 right-0 w-80 rounded-xl shadow-lg border py-1 z-50"
                         style="background: #FFFFFF; border-color: #E5E7EB;">
                        <div class="px-4 py-3 flex items-center justify-between" style="border-bottom: 1px solid #E5E7EB;">
                            <p class="text-sm font-semibold" style="color: #111827;">Notificaciones</p>
                            <span class="text-xs px-2 py-0.5 rounded-full" style="background: #EFF6FF; color: #2563EB;">
                                {{ $notificacionesPendientes ?? 0 }} nueva(s)
                            </span>
                        </div>
                        <div class="max-h-72 overflow-y-auto">
                            @forelse($notificaciones ?? [] as $notif)
                                <div class="px-4 py-3 flex gap-3 items-start transition-colors duration-100" style="border-bottom: 1px solid #F3F4F6;"
                                     onmouseover="this.style.background='#F9FAFB';" onmouseout="this.style.background='transparent';">
                                    <div class="flex items-center justify-center rounded-full shrink-0 mt-0.5" style="width: 8px; height: 8px; background: #2563EB;"></div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm" style="color: #374151;">{{ $notif['mensaje'] }}</p>
                                        <p class="text-xs mt-1" style="color: #9CA3AF;">{{ $notif['fecha'] }}</p>
                                    </div>
                                </div>
                            @empty
                                <div class="px-4 py-8 text-center">
                                    <svg class="mx-auto mb-2" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#D1D5DB" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                                    </svg>
                                    <p class="text-sm" style="color: #9CA3AF;">No hay notificaciones</p>
                                    <p class="text-xs mt-1" style="color: #D1D5DB;">Aparecerán aquí las actualizaciones de tus casos</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Usuario --}}
                <div class="relative" x-data="{ open: false }">
                    <div class="flex items-center gap-2 cursor-pointer" @click="open = !open">
                        <div class="flex items-center justify-center rounded-full" style="width: 32px; height: 32px; background: #1E3A5F; color: white; font-size: 12px; font-weight: 600;">
                            {{ strtoupper(substr(explode(' ', Auth::user()->usuario_nombre)[0] ?? 'U', 0, 1)) }}{{ strtoupper(substr(explode(' ', Auth::user()->usuario_nombre)[1] ?? '', 0, 1)) }}
                        </div>
                        <div class="text-sm hidden md:block" style="color: #111827;">
                            <span class="font-medium">{{ Auth::user()->usuario_nombre }}</span>
                        </div>
                        <svg :class="open ? 'rotate-180' : ''" class="transition-transform duration-200 hidden md:block" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #6B7280;">
                            <polyline points="6 9 12 15 18 9"/>
                        </svg>
                    </div>

                    {{-- Dropdown menú --}}
                    <div x-show="open" x-cloak @click.outside="open = false" @keydown.escape.window="open = false"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                         class="absolute top-12 right-0 w-48 rounded-lg shadow-lg border py-1 z-50"
                         style="background: #FFFFFF; border-color: #E5E7EB;">
                        <div class="px-4 py-2" style="border-bottom: 1px solid #E5E7EB;">
                            <p class="text-xs" style="color: #9CA3AF;">{{ Auth::user()->email }}</p>
                            <p class="text-xs font-medium mt-0.5" style="color: #6B7280;">{{ Auth::user()->rol?->rol_nombre ?? 'Sin rol' }}</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm" style="color: #EF4444; background: none; border: none; cursor: pointer;" onmouseover="this.style.background='#FEF2F2';" onmouseout="this.style.background='transparent';">
                                Cerrar sesión
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- Contenido dinámico --}}
        <main class="flex-1 overflow-auto p-4 md:p-6">
            @yield('content')
        </main>
    </div>
</div>

{{-- Alpine x-cloak --}}
<style>
    [x-cloak] { display: none !important; }
</style>

@stack('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Alertas de sesión (Success/Error)
        @if (session('success'))
            if (window.Swal) {
                Swal.fire({
                    title: '¡Éxito!',
                    text: "{{ session('success') }}",
                    icon: 'success',
                    confirmButtonColor: '#2563EB'
                });
            }
        @endif

        @if (session('error'))
            if (window.Swal) {
                Swal.fire({
                    title: 'Error',
                    text: "{{ session('error') }}",
                    icon: 'error',
                    confirmButtonColor: '#DC2626'
                });
            }
        @endif

        // Escuchador global de confirmaciones SweetAlert2
        document.addEventListener('submit', function (e) {
            const form = e.target;
            if (form && form.classList.contains('swal-confirm-form')) {
                e.preventDefault();
                const title = form.getAttribute('data-title') || '¿Estás seguro?';
                const text = form.getAttribute('data-text') || '';
                const icon = form.getAttribute('data-icon') || 'warning';
                const confirmText = form.getAttribute('data-confirm-text') || 'Confirmar';
                const cancelText = form.getAttribute('data-cancel-text') || 'Cancelar';
                const isWarning = icon === 'warning';

                if (window.Swal) {
                    Swal.fire({
                        title: title,
                        text: text,
                        icon: icon,
                        showCancelButton: true,
                        confirmButtonColor: isWarning ? '#DC2626' : '#2563EB',
                        cancelButtonColor: '#6B7280',
                        confirmButtonText: confirmText,
                        cancelButtonText: cancelText
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.classList.remove('swal-confirm-form');
                            form.submit();
                        }
                    });
                } else {
                    if (confirm(title)) {
                        form.submit();
                    }
                }
            }
        });
    });
</script>
<x:mobile-navigation />

@RegisterServiceWorkerScript
</body>
</html>
