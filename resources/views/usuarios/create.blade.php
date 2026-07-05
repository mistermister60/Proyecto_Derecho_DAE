@extends('layouts.app')

@section('title', 'Crear Usuario')

@section('content')
<form action="{{ route('usuarios.store') }}" method="POST">
    @csrf
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-6">
        <h1 class="text-xl font-bold" style="color: #111827;">Nuevo Usuario</h1>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <a href="{{ route('usuarios.index') }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-all min-h-[44px] flex items-center justify-center flex-1 sm:flex-none"
               style="background: #F3F4F6; color: #374151; border: 1px solid #E5E7EB;">
                Cancelar
            </a>
            <button type="submit" class="px-4 py-2 rounded-lg text-sm font-medium transition-all min-h-[44px] flex items-center justify-center flex-1 sm:flex-none"
                    style="background: #2563EB; color: white;" onmouseover="this.style.background='#1d4ed8';" onmouseout="this.style.background='#2563EB';">
                Guardar Usuario
            </button>
        </div>
    </div>

    <div class="rounded-xl p-5 mb-4" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
        <h3 class="text-sm font-semibold mb-4" style="color: #111827;">Datos del usuario</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Nombre completo</label>
                <input type="text" name="usuario_nombre" value="{{ old('usuario_nombre') }}" required placeholder="Ej: Juan Pérez" class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;">
                @error('usuario_nombre')
                <p class="text-xs mt-1" style="color: #DC2626;">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Correo electrónico</label>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="ejemplo@usap.edu" class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;">
                @error('email')
                <p class="text-xs mt-1" style="color: #DC2626;">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Contraseña</label>
                <input type="password" name="contrasena" required minlength="8" placeholder="Mínimo 8 caracteres, mayúscula y número" class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;">
                @error('contrasena')
                <p class="text-xs mt-1" style="color: #DC2626;">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Rol</label>
                <select name="rol_id" required class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;">
                    <option value="">Seleccionar rol...</option>
                    @foreach ($roles as $rol)
                        <option value="{{ $rol->rol_id }}" {{ old('rol_id') == $rol->rol_id ? 'selected' : '' }}>{{ $rol->rol_nombre }}</option>
                    @endforeach
                </select>
                @error('rol_id')
                <p class="text-xs mt-1" style="color: #DC2626;">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Procurador asociado (opcional)</label>
                <select name="procurador_id" class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;">
                    <option value="">Sin procurador...</option>
                    @foreach ($procuradores as $procurador)
                        <option value="{{ $procurador->procurador_id }}" {{ old('procurador_id') == $procurador->procurador_id ? 'selected' : '' }}>{{ $procurador->nombre_completo }}</option>
                    @endforeach
                </select>
                @error('procurador_id')
                <p class="text-xs mt-1" style="color: #DC2626;">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>
</form>
@endsection
