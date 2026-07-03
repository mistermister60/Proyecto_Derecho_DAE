@extends('layouts.app')

@section('title', 'Procuradores') 

@section('content')
<div x-data="{ 
    search: '', 
    statusFilter: 'activo',
    shouldShow(nombre, apellido, dni, telefono, estado) {
        let term = this.search.trim().toLowerCase();
        if (term !== '') {
            return nombre.toLowerCase().includes(term) || 
                   apellido.toLowerCase().includes(term) || 
                   dni.toLowerCase().includes(term) || 
                   (telefono && telefono.toLowerCase().includes(term));
        }
        return estado === this.statusFilter;
    }
}">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-5">
        <h1 class="text-xl font-bold" style="color: #111827;">Procuradores</h1>
        <a href="{{ route('procuradores.create') }}" class="flex items-center gap-1.5 px-4 py-1.5 rounded-lg text-sm font-medium transition-all"
           style="background: #2563EB; color: white;"
           onmouseover="this.style.background='#1d4ed8';" onmouseout="this.style.background='#2563EB';">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Nuevo procurador
        </a>
    </div>

    {{-- Buscador y Filtros --}}
    <div class="flex flex-col sm:flex-row gap-3 mb-5">
        <div class="relative flex-1">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#9CA3AF" stroke-width="2" class="absolute" style="top: 50%; left: 12px; transform: translateY(-50%);" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" x-model="search" placeholder="Buscar procuradores por nombre, DNI o teléfono..." class="w-full rounded-lg pl-9 pr-3 py-2 text-sm outline-none" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;">
        </div>
        <div class="flex items-center gap-1 bg-gray-100 p-1 rounded-lg self-start sm:self-auto" style="background: #F3F4F6; border: 1px solid #E5E7EB;">
            <button type="button" @click="statusFilter = 'activo'" class="px-4 py-1.5 rounded-md text-xs font-semibold transition-all outline-none"
                :style="statusFilter === 'activo' ? 'background: #FFFFFF; color: #111827; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #E5E7EB;' : 'color: #4B5563; border: 1px solid transparent;'">
                Activos
            </button>
            <button type="button" @click="statusFilter = 'inactivo'" class="px-4 py-1.5 rounded-md text-xs font-semibold transition-all outline-none"
                :style="statusFilter === 'inactivo' ? 'background: #FFFFFF; color: #111827; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #E5E7EB;' : 'color: #4B5563; border: 1px solid transparent;'">
                Inactivos
            </button>
        </div>
    </div>

    {{-- Tarjetas de procuradores --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @forelse ($procuradores as $procurador)
        <a href="{{ route('procuradores.show', $procurador->procurador_dni) }}" 
           class="procurador-card rounded-xl p-4 transition-all"
           x-show="shouldShow('{{ addslashes($procurador->procurador_nombre) }}', '{{ addslashes($procurador->procurador_apellido) }}', '{{ $procurador->procurador_dni }}', '{{ $procurador->procurador_telefono }}', '{{ $procurador->procurador_estado }}')"
           style="background: #FFFFFF; border: 1px solid #E5E7EB; display: block;"
           onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.08)'; this.style.borderColor='#93C5FD';" onmouseout="this.style.boxShadow='none'; this.style.borderColor='#E5E7EB';">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex items-center justify-center rounded-full shrink-0" style="width: 40px; height: 40px; background: #1E3A5F; color: white; font-size: 14px; font-weight: 600;">
                    {{ strtoupper(substr($procurador->procurador_nombre, 0, 1)) }}{{ strtoupper(substr($procurador->procurador_apellido, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-medium" style="color: #111827;">{{ $procurador->procurador_nombre }} {{ $procurador->procurador_apellido }}</p>
                    <p class="text-xs" style="color: #6B7280;">{{ $procurador->procurador_dni }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3 text-xs" style="color: #6B7280;">
                <span>{{ $procurador->procurador_telefono }}</span>
                <span class="ml-auto">{{ $procurador->casos_count }} {{ $procurador->casos_count === 1 ? 'caso' : 'casos' }}</span>
            </div>
            @if ($procurador->procurador_estado !== 'activo')
            <span class="inline-block mt-2 text-xs px-2 py-0.5 rounded" style="background: #FEE2E2; color: #DC2626;">Inactivo</span>
            @endif
        </a>
        @empty
        <div class="col-span-3 py-12 text-center">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#D1D5DB" stroke-width="1.5" class="mx-auto mb-3" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <p class="text-sm" style="color: #9CA3AF;">No hay procuradores registrados</p>
        </div>
        @endforelse

        <div x-show="[...document.querySelectorAll('.procurador-card')].every(el => el.style.display === 'none')" 
             class="col-span-3 py-12 text-center" 
             style="display: none;">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#D1D5DB" stroke-width="1.5" class="mx-auto mb-3" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <p class="text-sm" style="color: #9CA3AF;">No se encontraron procuradores que coincidan con la búsqueda o el filtro.</p>
        </div>
    </div>

    {{ $procuradores->links() }}
</div>
@endsection
