@extends('layouts.app')
{{--
    Vista: procuradores/show
    Propósito: Detalle completo de un procurador con información personal, carnet, email y lista de casos asignados. Permite editar el usuario asociado, activar o desactivar el procurador.
    Variables: $procurador (modelo Procurador con relaciones usuario y casos)
    @extends: layouts.app
    @section: content
--}}

@section('title', $procurador->nombre_completo)

@section('content')
<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-5">
        <div class="flex items-center gap-3">
            <div class="flex items-center justify-center rounded-full shrink-0" style="width: 48px; height: 48px; background: #1E3A5F; color: white; font-size: 18px; font-weight: 600;">
                {{ strtoupper(substr($procurador->procurador_nombre, 0, 1)) }}{{ strtoupper(substr($procurador->procurador_apellido, 0, 1)) }}
            </div>
            <div>
                <h1 class="text-xl font-bold" style="color: #111827;">{{ $procurador->nombre_completo }}</h1>
                <p class="text-sm" style="color: #6B7280;">{{ $procurador->procurador_dni }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            @if($procurador->usuario)
        <a href="{{ route('usuarios.edit', $procurador->usuario->usuario_id) }}" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all min-h-[44px] flex items-center"
           style="background: #EEF2F6; color: #1E40AF; border: 1px solid #DBEAFE;"
           onmouseover="this.style.background='#DBEAFE';" onmouseout="this.style.background='#EEF2F6';">
            Editar Usuario
        </a>

<a href="{{ route('procuradores.constancia', $procurador->procurador_dni) }}"
   target="_blank"
   class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all min-h-[44px] flex items-center"
   style="background: #DC2626; color: white; border: 1px solid #B91C1C;"
   onmouseover="this.style.background='#B91C1C';"
   onmouseout="this.style.background='#DC2626';">

    <svg xmlns="http://www.w3.org/2000/svg"
         class="w-4 h-4 mr-2"
         fill="none"
         viewBox="0 0 24 24"
         stroke="currentColor">
        <path stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
    </svg>

    Constancia PDF
</a>

            @endif
            <a href="{{ route('procuradores.edit', $procurador->procurador_dni) }}" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all min-h-[44px] flex items-center"
               style="background: #F3F4F6; color: #374151; border: 1px solid #E5E7EB;">
                Editar
            </a>
            @if ($procurador->procurador_estado === 'activo')
                <form action="{{ route('procuradores.destroy', $procurador->procurador_dni) }}" method="POST" class="swal-confirm-form" data-title="¿Desactivar este procurador?" data-text="El procurador pasará a estar inactivo en el sistema." data-icon="warning" data-confirm-text="Sí, desactivar" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all min-h-[44px]"
                            style="background: #FEE2E2; color: #DC2626; border: 1px solid #FECACA;"
                            onmouseover="this.style.background='#FECACA';" onmouseout="this.style.background='#FEE2E2';">
                        Desactivar
                    </button>
                </form>
            @else
                <form action="{{ route('procuradores.activar', $procurador->procurador_dni) }}" method="POST" class="swal-confirm-form" data-title="¿Activar este procurador?" data-text="El procurador volverá a estar activo en el sistema." data-icon="question" data-confirm-text="Sí, activar" style="display:inline;">
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

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Información personal --}}
        <div class="rounded-xl p-5" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
            <h3 class="text-sm font-semibold mb-4" style="color: #111827;">Información personal</h3>
            <dl class="space-y-3">
                <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">Nombre completo</dt><dd class="text-sm font-medium text-right" style="color: #111827;">{{ $procurador->procurador_nombre }} {{ $procurador->procurador_apellido }}</dd></div>
                <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">DNI</dt><dd class="text-sm font-medium text-right" style="color: #111827;">{{ $procurador->procurador_dni }}</dd></div>
                <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">Carnet</dt><dd class="text-sm font-medium text-right" style="color: #111827;">{{ $procurador->procurador_carnet ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">Fecha de nacimiento</dt><dd class="text-sm font-medium text-right" style="color: #111827;">{{ $procurador->procurador_fecha_nacimiento ? \Carbon\Carbon::parse($procurador->procurador_fecha_nacimiento)->format('d/m/Y') : '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">Género</dt><dd class="text-sm font-medium text-right" style="color: #111827;">{{ $procurador->procurador_genero ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">Email</dt><dd class="text-sm font-medium text-right" style="color: #111827;">{{ $procurador->procurador_email ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">Teléfono</dt><dd class="text-sm font-medium text-right" style="color: #111827;">{{ $procurador->procurador_telefono ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">Dirección</dt><dd class="text-sm font-medium text-right" style="color: #111827; max-width: 200px;">{{ $procurador->procurador_direccion ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">Estado</dt><dd class="text-sm font-medium text-right"><x-estado-badge :estado="$procurador->procurador_estado === 'activo' ? 'Activo' : 'Inactivo'" /></dd></div>
            </dl>
        </div>

        {{-- Casos asociados --}}
        <div class="rounded-xl p-5" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
            <h3 class="text-sm font-semibold mb-4" style="color: #111827;">Casos asociados</h3>
            @forelse ($procurador->casos as $caso)
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
