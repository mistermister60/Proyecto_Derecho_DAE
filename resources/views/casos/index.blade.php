@extends('layouts.app')

@section('title', 'Casos')

@section('content')
<div x-data="{ vista: 'tabla', search: '', filtroEstado: '', filtroTramite: '' }">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-5">
        <div class="flex items-center gap-3">
            <div class="flex rounded-lg overflow-hidden" style="border: 1px solid #E5E7EB;">
                <button @click="vista = 'tabla'"
                        class="px-3 py-1.5 text-sm font-medium transition-colors"
                        :style="vista === 'tabla' ? 'background: #2563EB; color: white;' : 'background: #FFFFFF; color: #6B7280;'">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="inline mr-1" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="3" y1="15" x2="21" y2="15"/></svg>
                    Tabla
                </button>
                <button @click="vista = 'kanban'"
                        class="px-3 py-1.5 text-sm font-medium transition-colors"
                        :style="vista === 'kanban' ? 'background: #2563EB; color: white;' : 'background: #FFFFFF; color: #6B7280;'">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="inline mr-1" aria-hidden="true"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    Kanban
                </button>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <select x-model="filtroEstado" class="text-sm rounded-lg px-3 py-1.5 outline-none" style="border: 1px solid #E5E7EB; color: #6B7280; background: #FFFFFF;">
                <option value="">Todos los estados</option>
                @foreach ($estados as $estado)
                <option value="{{ $estado->estado_nombre }}">{{ $estado->estado_nombre }}</option>
                @endforeach
            </select>

            <select x-model="filtroTramite" class="text-sm rounded-lg px-3 py-1.5 outline-none" style="border: 1px solid #E5E7EB; color: #6B7280; background: #FFFFFF;">
                <option value="">Todos los trámites</option>
                @foreach ($tramites as $tramite)
                <option value="{{ $tramite->tramite_nombre }}">{{ $tramite->tramite_nombre }}</option>
                @endforeach
            </select>

            <a href="{{ route('casos.create') }}" class="flex items-center gap-1.5 px-4 py-1.5 rounded-lg text-sm font-medium transition-all"
               style="background: #2563EB; color: white;"
               onmouseover="this.style.background='#1d4ed8';" onmouseout="this.style.background='#2563EB';">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Nuevo caso
            </a>
        </div>
    </div>

    {{-- ==================== VISTA TABLA ==================== --}}
    <div x-show="vista === 'tabla'">
        <x-tabla :encabezados="['No. Expediente', 'Cliente', 'Tipo de trámite', 'Parte', 'Procurador', 'Juzgado', 'Estado', 'Última actualización']">
            @forelse ($casos as $caso)
            <tr class="transition-colors border-t" style="border-color: #F3F4F6; cursor: pointer;" onmouseover="this.style.background='#F9FAFB';" onmouseout="this.style.background='transparent';" onclick="window.location='{{ route('casos.show', $caso->caso_numero_expediente) }}'">
                <td class="px-4 py-3 text-sm font-medium" style="color: #2563EB;">{{ $caso->caso_numero_expediente }}</td>
                <td class="px-4 py-3 text-sm">{{ $caso->cliente?->nombre_completo ?? 'N/A' }}</td>
                <td class="px-4 py-3 text-sm" style="color: #6B7280;">{{ $caso->tipoTramite?->tramite_nombre ?? 'N/A' }}</td>
                <td class="px-4 py-3 text-sm" style="color: #6B7280;">{{ $caso->caso_parte_representada }}</td>
                <td class="px-4 py-3 text-sm" style="color: #6B7280;">{{ $caso->procurador?->nombre_completo ?? 'N/A' }}</td>
                <td class="px-4 py-3 text-sm"><span class="px-2 py-0.5 rounded text-xs font-medium" style="background: #F3F4F6; color: #6B7280;">{{ $caso->caso_juzgado ?? 'N/A' }}</span></td>
                <td class="px-4 py-3"><x-estado-badge :estado="$caso->estado?->estado_nombre ?? 'N/A'" /></td>
                <td class="px-4 py-3 text-sm" style="color: #9CA3AF;">{{ $caso->created_at->format('d/m/Y') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-4 py-8 text-center text-sm" style="color: #9CA3AF;">No hay casos registrados</td>
            </tr>
            @endforelse
        </x-tabla>
    </div>

    {{-- ==================== VISTA KANBAN ==================== --}}
    <div x-show="vista === 'kanban'" class="flex gap-4 overflow-x-auto pb-4" style="min-height: 500px;">
        @foreach ($columnas as $estado => [$color, $tarjetas])
        <div class="flex flex-col rounded-xl shrink-0" style="width: 220px; background: #F3F4F6;">
            <div class="flex items-center gap-2 px-3 py-3">
                <span class="rounded-full" style="width: 8px; height: 8px; background: {{ $color }}; display: inline-block;"></span>
                <span class="text-xs font-semibold uppercase tracking-wider" style="color: #6B7280;">{{ $estado }}</span>
                <span class="ml-auto text-xs font-medium px-1.5 py-0.5 rounded-full" style="background: #E5E7EB; color: #6B7280;">{{ count($tarjetas) }}</span>
            </div>
            <div class="flex-1 px-3 pb-3 space-y-2">
                @forelse ($tarjetas as $exp => [$cliente, $tipo, $fecha])
                <div class="rounded-lg p-3 cursor-pointer transition-shadow" style="background: #FFFFFF; border: 1px solid #E5E7EB; box-shadow: 0 1px 2px rgba(0,0,0,0.04);" onclick="window.location='{{ route('casos.show', $exp) }}'" onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.08)';" onmouseout="this.style.boxShadow='0 1px 2px rgba(0,0,0,0.04)';">
                    <p class="text-sm font-medium" style="color: #111827;">{{ $cliente }}</p>
                    <p class="text-xs mt-1" style="color: #6B7280;">{{ $tipo }}</p>
                    @if ($fecha)
                    <div class="flex items-center gap-1 mt-2 text-xs" style="color: #F59E0B;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        {{ $fecha }}
                    </div>
                    @endif
                </div>
                @empty
                <div class="flex items-center justify-center py-8 text-xs" style="color: #9CA3AF;">
                    Sin casos
                </div>
                @endforelse
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
