@extends('layouts.app')
{{--
    Vista: demandados/show
    Propósito: Detalle completo de un demandado con información personal, laboral y lista de casos asociados. Permite editar, activar o desactivar el demandado.
    Variables: $demandado (modelo Demandado con relación casos)
    @extends: layouts.app
    @section: content
--}}

@section('title', $demandado->nombre_completo)

@section('content')
<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-5">
        <div class="flex items-center gap-3">
            <div class="flex items-center justify-center rounded-full shrink-0" style="width: 48px; height: 48px; background: #1E3A5F; color: white; font-size: 18px; font-weight: 600;">
                {{ strtoupper(substr($demandado->demandado_nombre, 0, 1)) }}{{ strtoupper(substr($demandado->demandado_apellido, 0, 1)) }}
            </div>
            <div>
                <h1 class="text-xl font-bold" style="color: #111827;">{{ $demandado->nombre_completo }}</h1>
                <p class="text-sm" style="color: #6B7280;">{{ $demandado->demandado_dni }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('demandados.edit', $demandado->demandado_dni) }}" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all min-h-[44px] flex items-center"
               style="background: #F3F4F6; color: #374151; border: 1px solid #E5E7EB;">
                Editar
            </a>
            @if ($demandado->demandado_estado === 'activo')
                <form action="{{ route('demandados.destroy', $demandado->demandado_dni) }}" method="POST" class="swal-confirm-form" data-title="¿Desactivar este demandado?" data-text="El demandado pasará a estar inactivo en el sistema." data-icon="warning" data-confirm-text="Sí, desactivar" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all min-h-[44px]"
                            style="background: #FEE2E2; color: #DC2626; border: 1px solid #FECACA;"
                            onmouseover="this.style.background='#FECACA';" onmouseout="this.style.background='#FEE2E2';">
                        Desactivar
                    </button>
                </form>
            @else
                <form action="{{ route('demandados.activar', $demandado->demandado_dni) }}" method="POST" class="swal-confirm-form" data-title="¿Activar este demandado?" data-text="El demandado volverá a estar activo en el sistema." data-icon="question" data-confirm-text="Sí, activar" style="display:inline;">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all min-h-[44px]"
                            style="background: #D1FAE5; color: #065F46; border: 1px solid #A7F3D0;"
                            onmouseover="this.style.background='#A7F3D0';" onmouseout="this.style.background='#D1FAE5';">
                        Activar
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Información personal --}}
        <div class="rounded-xl p-5" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
            <h3 class="text-sm font-semibold mb-4" style="color: #111827;">Información personal</h3>
            <dl class="space-y-3">
                <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">Nombre completo</dt><dd class="text-sm font-medium text-right" style="color: #111827;">{{ $demandado->nombre_completo }}</dd></div>
                <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">DNI</dt><dd class="text-sm font-medium text-right" style="color: #111827;">{{ $demandado->demandado_dni }}</dd></div>
                <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">Estado civil</dt><dd class="text-sm font-medium text-right" style="color: #111827;">{{ $demandado->demandado_estado_civil ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">Teléfono</dt><dd class="text-sm font-medium text-right" style="color: #111827;">{{ $demandado->demandado_telefono ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">Dirección</dt><dd class="text-sm font-medium text-right" style="color: #111827; max-width: 200px;">{{ $demandado->demandado_direccion ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">Estado</dt><dd class="text-sm font-medium text-right"><x-estado-badge :estado="$demandado->demandado_estado === 'activo' ? 'Activo' : 'Inactivo'" /></dd></div>
            </dl>
        </div>

        {{-- Información laboral --}}
        <div class="rounded-xl p-5" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
            <h3 class="text-sm font-semibold mb-4" style="color: #111827;">Información laboral</h3>
            <dl class="space-y-3">
                <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">Profesión / Oficio</dt><dd class="text-sm font-medium text-right" style="color: #111827;">{{ $demandado->demandado_profesion ?? 'No especificada' }}</dd></div>
                <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">Lugar de trabajo</dt><dd class="text-sm font-medium text-right" style="color: #111827;">{{ $demandado->demandado_lugar_trabajo ?? 'No especificado' }}</dd></div>
                <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">Teléfono trabajo</dt><dd class="text-sm font-medium text-right" style="color: #111827;">{{ $demandado->demandado_telefono_trabajo ?? '—' }}</dd></div>
            </dl>
        </div>

        {{-- Casos asociados --}}
        <div class="rounded-xl p-5" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
            <h3 class="text-sm font-semibold mb-4" style="color: #111827;">Casos asociados</h3>
            @forelse ($demandado->casos as $caso)
            <a href="{{ route('casos.show', $caso->caso_numero_expediente) }}" class="flex items-center gap-3 p-3 rounded-lg mb-2 transition-colors" style="border: 1px solid #E5E7EB;" onmouseover="this.style.background='#F9FAFB';" onmouseout="this.style.background='transparent';">
                <div>
                    <p class="text-sm font-medium" style="color: #2563EB;">{{ $caso->caso_numero_expediente }}</p>
                    <p class="text-xs" style="color: #6B7280;">{{ $caso->tipoTramite?->tramite_nombre ?? 'N/A' }}</p>
                </div>
                <div class="ml-auto">
                    <x-estado-badge :estado="$caso->estado?->estado_nombre ?? 'N/A'" />
                </div>
            </a>
            @empty
            <div class="py-8 text-center">
                <p class="text-sm" style="color: #9CA3AF;">No tiene casos asociados</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
