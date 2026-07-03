@extends('layouts.app')

@section('title', 'Clientes')

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
        <h1 class="text-xl font-bold" style="color: #111827;">Clientes</h1>
         <a href="{{ route('clientes.create') }}" class="flex items-center gap-1.5 px-4 py-1.5 rounded-lg text-sm font-medium transition-all bg-blue-600 text-white hover:bg-blue-700">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Nuevo cliente
        </a>
    </div>

    {{-- Buscador y Filtros --}}
    <div class="flex flex-col sm:flex-row gap-3 mb-5">
        <div class="relative flex-1">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#9CA3AF" stroke-width="2" class="absolute" style="top: 50%; left: 12px; transform: translateY(-50%);" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" x-model="search" placeholder="Buscar clientes por nombre, DNI o teléfono..." class="w-full rounded-lg pl-9 pr-3 py-2 text-sm outline-none" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;">
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

    {{-- Tarjetas de clientes --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @forelse ($clientes as $cliente)
        <a href="{{ route('clientes.show', $cliente->cliente_dni) }}" 
            class="cliente-card rounded-xl p-4 transition-all hover:shadow-md hover:border-blue-300 bg-white border border-gray-200"
            x-show="shouldShow(@js($cliente->cliente_nombre), @js($cliente->cliente_apellido), @js($cliente->cliente_dni), @js($cliente->cliente_telefono), @js($cliente->cliente_estado))"
            style="display: block;">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex items-center justify-center rounded-full shrink-0" style="width: 40px; height: 40px; background: #1E3A5F; color: white; font-size: 14px; font-weight: 600;">
                    {{ strtoupper(substr($cliente->cliente_nombre, 0, 1)) }}{{ strtoupper(substr($cliente->cliente_apellido, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-medium" style="color: #111827;">{{ $cliente->nombre_completo }}</p>
                    <p class="text-xs" style="color: #6B7280;">{{ $cliente->cliente_dni }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3 text-xs" style="color: #6B7280;">
                <span>{{ $cliente->cliente_telefono }}</span>
                <span class="ml-auto">{{ $cliente->casos_count }} {{ $cliente->casos_count === 1 ? 'caso' : 'casos' }}</span>
            </div>
            @if ($cliente->cliente_estado !== 'activo')
            <span class="inline-block mt-2 text-xs px-2 py-0.5 rounded" style="background: #FEE2E2; color: #DC2626;">Inactivo</span>
            @endif
        </a>
        @empty
        <div class="col-span-3 py-12 text-center">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#D1D5DB" stroke-width="1.5" class="mx-auto mb-3" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <p class="text-sm" style="color: #9CA3AF;">No hay clientes registrados</p>
        </div>
        @endforelse

        {{-- Mensaje si ningún cliente coincide con la búsqueda --}}
        <div x-show="[...document.querySelectorAll('.cliente-card')].every(el => el.style.display === 'none')" 
             class="col-span-3 py-12 text-center" 
             style="display: none;">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#D1D5DB" stroke-width="1.5" class="mx-auto mb-3" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <p class="text-sm" style="color: #9CA3AF;">No se encontraron clientes que coincidan con la búsqueda o el filtro.</p>
        </div>
    </div>

    {{ $clientes->links() }}
</div>
@endsection
