@extends('layouts.app')
{{--
    Vista: usuarios/index
    Propósito: Listado de usuarios del sistema con tabla, buscador en vivo y filtro activo/inactivo. Muestra nombre, email, rol, procurador asociado y acciones de editar/activar/desactivar.
    Variables: $usuarios (paginator de modelos Usuario), $estado (string para filtro), $roles (Collection)
    @extends: layouts.app
    @section: content
--}}

@section('title', 'Usuarios')

@section('content')
<div x-data="{ 
    search: '', 
    statusFilter: '{{ $estado ?? 'activo' }}',
    shouldShow(nombre, email, estado) {
        let term = this.search.trim().toLowerCase();
        if (term !== '') {
            return nombre.toLowerCase().includes(term) || 
                   email.toLowerCase().includes(term);
        }
        return estado === this.statusFilter;
    }
}">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-5">
        <h1 class="text-xl font-bold" style="color: #111827;">Usuarios del Sistema</h1>
        <a href="{{ route('usuarios.create') }}" class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium transition-all bg-blue-600 text-white hover:bg-blue-700 min-h-[44px]">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Nuevo usuario
        </a>
    </div>

    {{-- Buscador y Filtros --}}
    <div class="flex flex-col sm:flex-row gap-3 mb-5">
        <div class="relative flex-1">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#9CA3AF" stroke-width="2" class="absolute" style="top: 50%; left: 12px; transform: translateY(-50%);" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" x-model="search" placeholder="Buscar usuarios por nombre o email..." class="w-full rounded-lg pl-9 pr-3 py-2 text-sm outline-none" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;">
        </div>
        <div class="flex items-center gap-1 bg-gray-100 p-1 rounded-lg self-start sm:self-auto" style="background: #F3F4F6; border: 1px solid #E5E7EB;">
            <a href="{{ route('usuarios.index', ['estado' => 'activo']) }}" class="px-4 py-1.5 rounded-md text-xs font-semibold transition-all outline-none"
               style="{{ ($estado ?? 'activo') === 'activo' ? 'background: #FFFFFF; color: #111827; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #E5E7EB;' : 'color: #4B5563; border: 1px solid transparent;' }}">
                Activos
            </a>
            <a href="{{ route('usuarios.index', ['estado' => 'inactivo']) }}" class="px-4 py-1.5 rounded-md text-xs font-semibold transition-all outline-none"
               style="{{ ($estado ?? 'activo') === 'inactivo' ? 'background: #FFFFFF; color: #111827; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #E5E7EB;' : 'color: #4B5563; border: 1px solid transparent;' }}">
                Inactivos
            </a>
        </div>
    </div>

    {{-- Tabla de usuarios --}}
    <div class="rounded-xl overflow-hidden" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr style="background: #F9FAFB; border-bottom: 1px solid #E5E7EB;">
                    <th class="text-left px-4 py-3 font-semibold" style="color: #6B7280;">Nombre</th>
                    <th class="text-left px-4 py-3 font-semibold" style="color: #6B7280;">Email</th>
                    <th class="text-left px-4 py-3 font-semibold" style="color: #6B7280;">Rol</th>
                    <th class="text-left px-4 py-3 font-semibold" style="color: #6B7280;">Procurador</th>
                    <th class="text-center px-4 py-3 font-semibold" style="color: #6B7280;">Estado</th>
                    <th class="text-right px-4 py-3 font-semibold" style="color: #6B7280;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($usuarios as $usuario)
                <tr class="usuario-row transition-colors hover:bg-gray-50" style="border-bottom: 1px solid #F3F4F6;"
                    x-show="shouldShow(@js($usuario->usuario_nombre), @js($usuario->email), @js($usuario->usuario_estado))">
                    <td class="px-4 py-3">
                        <a href="{{ route('usuarios.show', $usuario->usuario_id) }}" class="font-medium" style="color: #111827;">{{ $usuario->usuario_nombre }}</a>
                    </td>
                    <td class="px-4 py-3" style="color: #6B7280;">{{ $usuario->email }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded text-xs font-medium" style="background: #EFF6FF; color: #2563EB;">
                            {{ $usuario->rol?->rol_nombre ?? '—' }}
                        </span>
                    </td>
                    <td class="px-4 py-3" style="color: #6B7280;">
                        {{ $usuario->procurador?->nombre_completo ?? '—' }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        <x-estado-badge :estado="$usuario->usuario_estado === 'activo' ? 'Activo' : 'Inactivo'" />
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('usuarios.edit', $usuario->usuario_id) }}" class="px-2.5 py-1 rounded text-xs font-medium transition-all"
                               style="background: #F3F4F6; color: #374151; border: 1px solid #E5E7EB;"
                               onmouseover="this.style.background='#E5E7EB';" onmouseout="this.style.background='#F3F4F6';">Editar</a>
                            @if ($usuario->usuario_estado === 'activo')
                                <form action="{{ route('usuarios.destroy', $usuario->usuario_id) }}" method="POST" class="swal-confirm-form" data-title="¿Desactivar usuario?" data-text="El usuario no podrá iniciar sesión." data-icon="warning" data-confirm-text="Sí, desactivar" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1 rounded text-xs font-medium transition-all"
                                            style="background: #FEE2E2; color: #DC2626; border: 1px solid #FECACA;"
                                            onmouseover="this.style.background='#FECACA';" onmouseout="this.style.background='#FEE2E2';">Desactivar</button>
                                </form>
                            @else
                                <form action="{{ route('usuarios.activar', $usuario->usuario_id) }}" method="POST" class="swal-confirm-form" data-title="¿Activar usuario?" data-text="El usuario podrá iniciar sesión nuevamente." data-icon="question" data-confirm-text="Sí, activar" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 rounded text-xs font-medium transition-all"
                                            style="background: #D1FAE5; color: #065F46; border: 1px solid #A7F3D0;"
                                            onmouseover="this.style.background='#A7F3D0';" onmouseout="this.style.background='#D1FAE5';">Activar</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-12 text-center" style="color: #9CA3AF;">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#D1D5DB" stroke-width="1.5" class="mx-auto mb-3" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        <p>No hay usuarios registrados</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    {{ $usuarios->links() }}
</div>
@endsection
