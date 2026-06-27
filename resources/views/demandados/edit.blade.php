@extends('layouts.app')

@section('title', 'Editar Demandado')

@section('content')
<form action="{{ route('demandados.update', $demandado->demandado_dni) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-bold" style="color: #111827;">Editar Demandado</h1>
        <div class="flex items-center gap-2">
            <a href="{{ route('demandados.show', $demandado->demandado_dni) }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-all"
               style="background: #F3F4F6; color: #374151; border: 1px solid #E5E7EB;">
                Cancelar
            </a>
            <button type="submit" class="px-4 py-2 rounded-lg text-sm font-medium transition-all"
                    style="background: #2563EB; color: white;" onmouseover="this.style.background='#1d4ed8';" onmouseout="this.style.background='#2563EB';">
                Guardar cambios
            </button>
        </div>
    </div>

    <div class="rounded-xl p-5 mb-4" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
        <h3 class="text-sm font-semibold mb-4" style="color: #111827;">Datos personales</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Nombre</label>
                <input type="text" name="demandado_nombre" value="{{ old('demandado_nombre', $demandado->demandado_nombre) }}" required placeholder="Ej: María" class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;">
                @error('demandado_nombre')
                <p class="text-xs mt-1" style="color: #DC2626;">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Apellido</label>
                <input type="text" name="demandado_apellido" value="{{ old('demandado_apellido', $demandado->demandado_apellido) }}" required placeholder="Ej: López" class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;">
                @error('demandado_apellido')
                <p class="text-xs mt-1" style="color: #DC2626;">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">DNI</label>
                <input type="text" name="demandado_dni" value="{{ old('demandado_dni', $demandado->demandado_dni) }}" required placeholder="Ej: 12345678" class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;">
                @error('demandado_dni')
                <p class="text-xs mt-1" style="color: #DC2626;">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Estado civil</label>
                <select name="demandado_estado_civil" class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;">
                    <option value="">Seleccionar...</option>
                    <option value="Soltero/a" {{ old('demandado_estado_civil', $demandado->demandado_estado_civil) == 'Soltero/a' ? 'selected' : '' }}>Soltero/a</option>
                    <option value="Casado/a" {{ old('demandado_estado_civil', $demandado->demandado_estado_civil) == 'Casado/a' ? 'selected' : '' }}>Casado/a</option>
                    <option value="Divorciado/a" {{ old('demandado_estado_civil', $demandado->demandado_estado_civil) == 'Divorciado/a' ? 'selected' : '' }}>Divorciado/a</option>
                    <option value="Viudo/a" {{ old('demandado_estado_civil', $demandado->demandado_estado_civil) == 'Viudo/a' ? 'selected' : '' }}>Viudo/a</option>
                    <option value="Unión libre" {{ old('demandado_estado_civil', $demandado->demandado_estado_civil) == 'Unión libre' ? 'selected' : '' }}>Unión libre</option>
                </select>
                @error('demandado_estado_civil')
                <p class="text-xs mt-1" style="color: #DC2626;">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Teléfono</label>
                <input type="text" name="demandado_telefono" value="{{ old('demandado_telefono', $demandado->demandado_telefono) }}" placeholder="Ej: 999 888 777" class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;">
                @error('demandado_telefono')
                <p class="text-xs mt-1" style="color: #DC2626;">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Dirección</label>
                <input type="text" name="demandado_direccion" value="{{ old('demandado_direccion', $demandado->demandado_direccion) }}" placeholder="Ej: Av. Principal 123" class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;">
                @error('demandado_direccion')
                <p class="text-xs mt-1" style="color: #DC2626;">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <div class="rounded-xl p-5 mb-4" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
        <h3 class="text-sm font-semibold mb-4" style="color: #111827;">Información laboral</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Profesión</label>
                <input type="text" name="demandado_profesion" value="{{ old('demandado_profesion', $demandado->demandado_profesion) }}" placeholder="Ej: Abogado" class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;">
                @error('demandado_profesion')
                <p class="text-xs mt-1" style="color: #DC2626;">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Lugar de trabajo</label>
                <input type="text" name="demandado_lugar_trabajo" value="{{ old('demandado_lugar_trabajo', $demandado->demandado_lugar_trabajo) }}" placeholder="Ej: Despacho ABC" class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;">
                @error('demandado_lugar_trabajo')
                <p class="text-xs mt-1" style="color: #DC2626;">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Teléfono de trabajo</label>
                <input type="text" name="demandado_telefono_trabajo" value="{{ old('demandado_telefono_trabajo', $demandado->demandado_telefono_trabajo) }}" placeholder="Ej: 999 888 777" class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;">
                @error('demandado_telefono_trabajo')
                <p class="text-xs mt-1" style="color: #DC2626;">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>
</form>
@endsection
