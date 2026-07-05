@extends('layouts.app')

@section('title', $usuario->usuario_nombre)

@section('content')
<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-5">
        <div class="flex items-center gap-3">
            <div class="flex items-center justify-center rounded-full shrink-0" style="width: 48px; height: 48px; background: #1E3A5F; color: white; font-size: 18px; font-weight: 600;">
                {{ strtoupper(substr(explode(' ', $usuario->usuario_nombre)[0] ?? 'U', 0, 1)) }}{{ strtoupper(substr(explode(' ', $usuario->usuario_nombre)[1] ?? '', 0, 1)) }}
            </div>
            <div>
                <h1 class="text-xl font-bold" style="color: #111827;">{{ $usuario->usuario_nombre }}</h1>
                <p class="text-sm" style="color: #6B7280;">{{ $usuario->email }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('usuarios.edit', $usuario->usuario_id) }}" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all min-h-[44px] flex items-center"
               style="background: #F3F4F6; color: #374151; border: 1px solid #E5E7EB;">
                Editar
            </a>
            @if ($usuario->usuario_estado === 'activo')
                <form action="{{ route('usuarios.destroy', $usuario->usuario_id) }}" method="POST" class="swal-confirm-form" data-title="¿Desactivar usuario?" data-text="El usuario no podrá iniciar sesión." data-icon="warning" data-confirm-text="Sí, desactivar" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all min-h-[44px]"
                            style="background: #FEE2E2; color: #DC2626; border: 1px solid #FECACA;"
                            onmouseover="this.style.background='#FECACA';" onmouseout="this.style.background='#FEE2E2';">
                        Desactivar
                    </button>
                </form>
            @else
                <form action="{{ route('usuarios.activar', $usuario->usuario_id) }}" method="POST" class="swal-confirm-form" data-title="¿Activar usuario?" data-text="El usuario podrá iniciar sesión nuevamente." data-icon="question" data-confirm-text="Sí, activar" style="display:inline;">
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
        {{-- Información del usuario --}}
        <div class="rounded-xl p-5" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
            <h3 class="text-sm font-semibold mb-4" style="color: #111827;">Información del usuario</h3>
            <dl class="space-y-3">
                <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">Nombre</dt><dd class="text-sm font-medium text-right" style="color: #111827;">{{ $usuario->usuario_nombre }}</dd></div>
                <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">Email</dt><dd class="text-sm font-medium text-right" style="color: #111827;">{{ $usuario->email }}</dd></div>
                <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">Rol</dt><dd class="text-sm font-medium text-right"><span class="px-2 py-0.5 rounded text-xs font-medium" style="background: #EFF6FF; color: #2563EB;">{{ $usuario->rol?->rol_nombre ?? '—' }}</span></dd></div>
                <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">Procurador</dt><dd class="text-sm font-medium text-right" style="color: #111827;">{{ $usuario->procurador?->nombre_completo ?? 'No asociado' }}</dd></div>
                <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">Estado</dt><dd class="text-sm font-medium text-right"><x-estado-badge :estado="$usuario->usuario_estado === 'activo' ? 'Activo' : 'Inactivo'" /></dd></div>
                <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">Registrado</dt><dd class="text-sm font-medium text-right" style="color: #111827;">{{ $usuario->created_at ? $usuario->created_at->format('d/m/Y') : '—' }}</dd></div>
            </dl>
        </div>

        {{-- Información del procurador --}}
        <div class="rounded-xl p-5" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
            <h3 class="text-sm font-semibold mb-4" style="color: #111827;">Procurador asociado</h3>
            @if ($usuario->procurador)
                <dl class="space-y-3">
                    <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">Nombre</dt><dd class="text-sm font-medium text-right" style="color: #111827;">{{ $usuario->procurador->nombre_completo }}</dd></div>
                    <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">DNI</dt><dd class="text-sm font-medium text-right" style="color: #111827;">{{ $usuario->procurador->procurador_dni }}</dd></div>
                    <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">Teléfono</dt><dd class="text-sm font-medium text-right" style="color: #111827;">{{ $usuario->procurador->procurador_telefono ?? '—' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">Email</dt><dd class="text-sm font-medium text-right" style="color: #111827;">{{ $usuario->procurador->procurador_email ?? '—' }}</dd></div>
                </dl>
                <a href="{{ route('procuradores.show', $usuario->procurador->procurador_dni) }}" class="inline-block mt-3 text-xs font-medium" style="color: #2563EB;">Ver procurador →</a>
            @else
                <div class="py-8 text-center">
                    <p class="text-sm" style="color: #9CA3AF;">No tiene procurador asociado</p>
                </div>
            @endif
        </div>

        {{-- Casos --}}
        <div class="rounded-xl p-5" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
            <h3 class="text-sm font-semibold mb-4" style="color: #111827;">Actividad reciente</h3>
            @if ($usuario->procurador && $usuario->procurador->casos->count() > 0)
                @foreach ($usuario->procurador->casos->take(5) as $caso)
                <a href="{{ route('casos.show', $caso->caso_numero_expediente) }}" class="flex items-center gap-3 p-3 rounded-lg mb-2 transition-colors" style="border: 1px solid #E5E7EB;" onmouseover="this.style.background='#F9FAFB';" onmouseout="this.style.background='transparent';">
                    <div>
                        <p class="text-sm font-medium" style="color: #2563EB;">{{ $caso->caso_numero_expediente }}</p>
                        <p class="text-xs" style="color: #6B7280;">{{ $caso->tipoTramite?->tramite_nombre ?? 'N/A' }}</p>
                    </div>
                    <div class="ml-auto">
                        <x-estado-badge :estado="$caso->estado?->estado_nombre ?? 'N/A'" />
                    </div>
                </a>
                @endforeach
            @else
                <div class="py-8 text-center">
                    <p class="text-sm" style="color: #9CA3AF;">Sin actividad registrada</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
