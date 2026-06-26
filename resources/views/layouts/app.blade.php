<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Consultorio Jurídico - USAP') | Consultorio Jurídico</title>

    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

<div class="flex h-screen w-full overflow-hidden">
    {{-- ==================== SIDEBAR ==================== --}}
    <aside class="flex flex-col shrink-0 h-full" style="width: 240px; background: #1E3A5F; border-right: 1px solid rgba(255,255,255,0.08);">

        {{-- Logo --}}
        <div class="flex items-center gap-3 px-5 py-5" style="border-bottom: 1px solid rgba(255,255,255,0.1);">
            <div class="flex items-center justify-center rounded-lg shrink-0" style="width: 36px; height: 36px; background: rgba(37,99,235,0.9);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
                $navItems = [
                    ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard'],
                    ['route' => 'casos.index', 'label' => 'Casos', 'icon' => 'casos'],
                    ['route' => 'clientes.index', 'label' => 'Clientes', 'icon' => 'clientes'],
                    ['route' => 'agenda.index', 'label' => 'Audiencias', 'icon' => 'agenda'],
                ];
            @endphp

            @foreach ($navItems as $item)
                @php
                    $isActive = str_starts_with($current, explode('.', $item['route'])[0]);
                @endphp
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all duration-150"
                   style="{{ $isActive ? 'background: rgba(37,99,235,0.2); color: #FFFFFF;' : 'color: rgba(255,255,255,0.65); hover:background: rgba(255,255,255,0.08);' }}"
                   onmouseover="this.style.background='{{ $isActive ? 'rgba(37,99,235,0.2)' : 'rgba(255,255,255,0.08)' }}'; this.style.color='#FFFFFF';"
                   onmouseout="this.style.background='{{ $isActive ? 'rgba(37,99,235,0.2)' : 'transparent' }}'; this.style.color='{{ $isActive ? '#FFFFFF' : 'rgba(255,255,255,0.65)' }}';">
                    @if ($item['icon'] === 'dashboard')
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                            <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
                        </svg>
                    @elseif ($item['icon'] === 'casos')
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
                        </svg>
                    @elseif ($item['icon'] === 'clientes')
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    @elseif ($item['icon'] === 'agenda')
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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

        {{-- Nuevo caso button --}}
        <div class="px-3 pb-4">
            <a href="{{ route('casos.create') }}"
               class="flex items-center justify-center gap-2 w-full py-2.5 rounded-lg text-sm font-medium transition-all duration-150"
               style="background: #2563EB; color: white;"
               onmouseover="this.style.background='#1d4ed8';"
               onmouseout="this.style.background='#2563EB';">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Nuevo caso
            </a>
        </div>
    </aside>

    {{-- ==================== CONTENIDO PRINCIPAL ==================== --}}
    <div class="flex flex-col flex-1 h-full overflow-hidden">

        {{-- Topbar --}}
        <header class="flex items-center justify-between shrink-0 px-6 py-3" style="background: #FFFFFF; border-bottom: 1px solid #E5E7EB;">
            <div class="flex items-center gap-3">
                <h1 class="text-lg font-semibold" style="color: #111827;">@yield('title', 'Dashboard')</h1>
            </div>

            <div class="flex items-center gap-4">
                {{-- Buscador --}}
                <div class="relative" x-data="{ search: '' }">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#9CA3AF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input type="text" x-model="search" placeholder="Buscar..."
                           class="pl-9 pr-3 py-1.5 rounded-lg text-sm outline-none transition-all duration-150"
                           style="background: #F7F8FA; border: 1px solid #E5E7EB; color: #111827; width: 220px;"
                           onfocus="this.style.borderColor='#2563EB'; this.style.width='260px';"
                           onblur="this.style.borderColor='#E5E7EB'; this.style.width='220px';">
                </div>

                {{-- Notificaciones --}}
                <button class="relative p-2 rounded-lg transition-colors duration-150" style="color: #6B7280;" onmouseover="this.style.background='#F3F4F6';" onmouseout="this.style.background='transparent';">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                    </svg>
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full" style="background: #EF4444;"></span>
                </button>

                {{-- Usuario --}}
                <div class="flex items-center gap-2 cursor-pointer" x-data="{ open: false }" @click.outside="open = false">
                    <div class="flex items-center justify-center rounded-full" style="width: 32px; height: 32px; background: #1E3A5F; color: white; font-size: 12px; font-weight: 600;">
                        {{ strtoupper(substr(explode(' ', Auth::user()->usuario_nombre)[0] ?? 'U', 0, 1)) }}{{ strtoupper(substr(explode(' ', Auth::user()->usuario_nombre)[1] ?? '', 0, 1)) }}
                    </div>
                    <div class="text-sm" style="color: #111827;">
                        <span class="font-medium">{{ Auth::user()->usuario_nombre }}</span>
                    </div>
                    <svg @click="open = !open" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #6B7280; cursor: pointer;">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>

                    {{-- Dropdown menú --}}
                    <div x-show="open" x-cloak
                         class="absolute top-14 right-4 w-48 rounded-lg shadow-lg border py-1 z-50"
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
        <main class="flex-1 overflow-auto p-6">
            @yield('content')
        </main>
    </div>
</div>

{{-- Alpine x-cloak --}}
<style>
    [x-cloak] { display: none !important; }
</style>

@stack('scripts')
</body>
</html>
