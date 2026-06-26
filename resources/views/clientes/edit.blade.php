@extends('layouts.app')

@section('title', 'Editar Cliente')

@section('content')
<form action="{{ route('clientes.update', $cliente->cliente_dni) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-bold" style="color: #111827;">Editar Cliente</h1>
        <div class="flex items-center gap-2">
            <a href="{{ route('clientes.show', $cliente->cliente_dni) }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-all"
               style="background: #F3F4F6; color: #374151; border: 1px solid #E5E7EB;">
                Cancelar
            </a>
            <button type="submit" class="px-4 py-2 rounded-lg text-sm font-medium transition-all"
                    style="background: #2563EB; color: white;" onmouseover="this.style.background='#1d4ed8';" onmouseout="this.style.background='#2563EB';">
                Actualizar Cliente
            </button>
        </div>
    </div>

    <div class="rounded-xl p-5 mb-4" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
        <h3 class="text-sm font-semibold mb-4" style="color: #111827;">Datos personales</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Nombre</label>
                <input type="text" name="cliente_nombre" value="{{ old('cliente_nombre', $cliente->cliente_nombre) }}" required placeholder="Ej: María" class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;">
                @error('cliente_nombre')
                <p class="text-xs mt-1" style="color: #DC2626;">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Apellido</label>
                <input type="text" name="cliente_apellido" value="{{ old('cliente_apellido', $cliente->cliente_apellido) }}" required placeholder="Ej: López" class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;">
                @error('cliente_apellido')
                <p class="text-xs mt-1" style="color: #DC2626;">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">DNI</label>
                <input type="number" inputmode="numeric" step="1" min="0" name="cliente_dni" value="{{ old('cliente_dni', $cliente->cliente_dni) }}" required placeholder="Ej: 12345678" class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;" oninput="this.value=this.value.replace(/[^0-9]/g,'');">
                @error('cliente_dni')
                <p class="text-xs mt-1" style="color: #DC2626;">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Estado civil</label>
                <select name="cliente_estado_civil" required class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;">
                    <option value="" {{ old('cliente_estado_civil', $cliente->cliente_estado_civil) == '' ? 'selected' : '' }}>Seleccionar...</option>
                    <option value="Soltero/a" {{ old('cliente_estado_civil', $cliente->cliente_estado_civil) == 'Soltero/a' ? 'selected' : '' }}>Soltero/a</option>
                    <option value="Casado/a" {{ old('cliente_estado_civil', $cliente->cliente_estado_civil) == 'Casado/a' ? 'selected' : '' }}>Casado/a</option>
                    <option value="Divorciado/a" {{ old('cliente_estado_civil', $cliente->cliente_estado_civil) == 'Divorciado/a' ? 'selected' : '' }}>Divorciado/a</option>
                    <option value="Viudo/a" {{ old('cliente_estado_civil', $cliente->cliente_estado_civil) == 'Viudo/a' ? 'selected' : '' }}>Viudo/a</option>
                    <option value="Unión libre" {{ old('cliente_estado_civil', $cliente->cliente_estado_civil) == 'Unión libre' ? 'selected' : '' }}>Unión libre</option>
                </select>
                <p class="text-xs mt-1" style="color: #6B7280;">Seleccione una única opción.</p>
                @error('cliente_estado_civil')
                <p class="text-xs mt-1" style="color: #DC2626;">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Teléfono</label>
                <input type="text" name="cliente_telefono" value="{{ old('cliente_telefono', $cliente->cliente_telefono) }}" required placeholder="Ej: 999 888 777" class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;">
                @error('cliente_telefono')
                <p class="text-xs mt-1" style="color: #DC2626;">{{ $message }}</p>
                @enderror
            </div>
            <div class="md:col-span-2">
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Dirección</label>
                <input type="text" name="cliente_direccion" value="{{ old('cliente_direccion', $cliente->cliente_direccion) }}" required placeholder="Ej: Av. Principal 123" class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;">
                @error('cliente_direccion')
                <p class="text-xs mt-1" style="color: #DC2626;">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <div class="rounded-xl p-5 mb-4" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
        <h3 class="text-sm font-semibold mb-4" style="color: #111827;">Información laboral y familiar</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Número de hijos</label>
                <input type="number" name="cliente_numero_hijos" value="{{ old('cliente_numero_hijos', $cliente->cliente_numero_hijos ?? 0) }}" min="0" class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;">
                @error('cliente_numero_hijos')
                <p class="text-xs mt-1" style="color: #DC2626;">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Profesión</label>
                <input type="text" name="cliente_profesion" value="{{ old('cliente_profesion', $cliente->cliente_profesion) }}" placeholder="Ej: Abogado" class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;">
                @error('cliente_profesion')
                <p class="text-xs mt-1" style="color: #DC2626;">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Lugar de trabajo</label>
                <input type="text" name="cliente_lugar_trabajo" value="{{ old('cliente_lugar_trabajo', $cliente->cliente_lugar_trabajo) }}" placeholder="Ej: Despacho ABC" class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;">
                @error('cliente_lugar_trabajo')
                <p class="text-xs mt-1" style="color: #DC2626;">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Dirección de trabajo</label>
                <input type="text" name="cliente_direccion_trabajo" value="{{ old('cliente_direccion_trabajo', $cliente->cliente_direccion_trabajo) }}" placeholder="Ej: Av. Principal 456" class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;">
                @error('cliente_direccion_trabajo')
                <p class="text-xs mt-1" style="color: #DC2626;">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Teléfono de trabajo</label>
                <input type="text" name="cliente_telefono_trabajo" value="{{ old('cliente_telefono_trabajo', $cliente->cliente_telefono_trabajo) }}" placeholder="Ej: 999 888 777" class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;">
                @error('cliente_telefono_trabajo')
                <p class="text-xs mt-1" style="color: #DC2626;">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Salario mensual</label>
                <input type="number" step="0.01" name="cliente_salario_mensual" value="{{ old('cliente_salario_mensual', $cliente->cliente_salario_mensual) }}" placeholder="Ej: 3500.50" class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;">
                @error('cliente_salario_mensual')
                <p class="text-xs mt-1" style="color: #DC2626;">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>
</form>
@endsection