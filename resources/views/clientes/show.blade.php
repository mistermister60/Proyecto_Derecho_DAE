@extends('layouts.app')

@section('title', $cliente->nombre_completo)

@section('content')
<div>
    {{-- Header --}}
    <div class="flex items-center justify-between mb-5">
        <div class="flex items-center gap-3">
            <div class="flex items-center justify-center rounded-full" style="width: 48px; height: 48px; background: #1E3A5F; color: white; font-size: 18px; font-weight: 600;">
                {{ strtoupper(substr($cliente->cliente_nombre, 0, 1)) }}{{ strtoupper(substr($cliente->cliente_apellido, 0, 1)) }}
            </div>
            <div>
                <h1 class="text-xl font-bold" style="color: #111827;">{{ $cliente->nombre_completo }}</h1>
                <p class="text-sm" style="color: #6B7280;">{{ $cliente->cliente_dni }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('clientes.edit', $cliente->cliente_dni) }}" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all"
               style="background: #F3F4F6; color: #374151; border: 1px solid #E5E7EB;">
                Editar
            </a>
            <form action="{{ route('clientes.destroy', $cliente->cliente_dni) }}" method="POST" onsubmit="return confirm('¿Desactivar este cliente?');" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all"
                        style="background: #FEE2E2; color: #DC2626; border: 1px solid #FECACA;"
                        onmouseover="this.style.background='#FECACA';" onmouseout="this.style.background='#FEE2E2';">
                    Desactivar
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-6">
        {{-- Información personal --}}
        <div class="rounded-xl p-5" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
            <h3 class="text-sm font-semibold mb-4" style="color: #111827;">Información personal</h3>
            <dl class="space-y-3">
                <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">Nombre completo</dt><dd class="text-sm font-medium text-right" style="color: #111827;">{{ $cliente->nombre_completo }}</dd></div>
                <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">DNI</dt><dd class="text-sm font-medium text-right" style="color: #111827;">{{ $cliente->cliente_dni }}</dd></div>
                <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">Estado civil</dt><dd class="text-sm font-medium text-right" style="color: #111827;">{{ $cliente->cliente_estado_civil }}</dd></div>
                <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">Número de hijos</dt><dd class="text-sm font-medium text-right" style="color: #111827;">{{ $cliente->cliente_numero_hijos ?? 0 }}</dd></div>
                <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">Teléfono</dt><dd class="text-sm font-medium text-right" style="color: #111827;">{{ $cliente->cliente_telefono }}</dd></div>
                <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">Dirección</dt><dd class="text-sm font-medium text-right" style="color: #111827; max-width: 200px;">{{ $cliente->cliente_direccion }}</dd></div>
                <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">Estado</dt><dd class="text-sm font-medium text-right"><x-estado-badge :estado="$cliente->cliente_estado === 'activo' ? 'Activo' : 'Inactivo'" /></dd></div>
            </dl>
        </div>

        {{-- Información laboral --}}
        <div class="rounded-xl p-5" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
            <h3 class="text-sm font-semibold mb-4" style="color: #111827;">Información laboral</h3>
            <dl class="space-y-3">
                <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">Profesión / Oficio</dt><dd class="text-sm font-medium text-right" style="color: #111827;">{{ $cliente->cliente_profesion ?? 'No especificada' }}</dd></div>
                <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">Lugar de trabajo</dt><dd class="text-sm font-medium text-right" style="color: #111827;">{{ $cliente->cliente_lugar_trabajo ?? 'No especificado' }}</dd></div>
                <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">Dirección trabajo</dt><dd class="text-sm font-medium text-right" style="color: #111827; max-width: 200px;">{{ $cliente->cliente_direccion_trabajo ?? 'No especificada' }}</dd></div>
                <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">Teléfono trabajo</dt><dd class="text-sm font-medium text-right" style="color: #111827;">{{ $cliente->cliente_telefono_trabajo ?? 'No especificado' }}</dd></div>
                <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">Salario mensual</dt><dd class="text-sm font-medium text-right" style="color: #111827;">{{ $cliente->cliente_salario_mensual > 0 ? 'L. ' . number_format($cliente->cliente_salario_mensual, 2) : 'No especificado' }}</dd></div>
            </dl>

            @if ($cliente->cliente_nombres_hijos)
            <div class="mt-4 pt-4" style="border-top: 1px solid #E5E7EB;">
                <h4 class="text-xs font-semibold mb-2" style="color: #6B7280;">HIJOS</h4>
                <p class="text-sm" style="color: #374151;">{{ $cliente->cliente_nombres_hijos }}</p>
            </div>
            @endif
        </div>

        {{-- Casos asociados --}}
        <div class="rounded-xl p-5" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
            <h3 class="text-sm font-semibold mb-4" style="color: #111827;">Casos asociados</h3>
            @forelse ($cliente->casos as $caso)
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
