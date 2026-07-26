@extends('layouts.app')
{{--
    Vista: procuradores/create
    Propósito: Formulario para crear un nuevo procurador. Incluye datos personales, carnet profesional, fecha de nacimiento, género, email y dirección.
    Variables: ninguna (formulario vacío)
    @extends: layouts.app
    @section: content
--}}

@section('title', 'Crear Procurador')

@section('content')
<form action="{{ route('procuradores.store') }}" method="POST">
    @csrf
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-6">
        <h1 class="text-xl font-bold" style="color: #111827;">Nuevo Procurador</h1>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <a href="{{ route('procuradores.index') }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-all min-h-[44px] flex items-center justify-center flex-1 sm:flex-none"
               style="background: #F3F4F6; color: #374151; border: 1px solid #E5E7EB;">
                Cancelar
            </a>
            <button type="submit" class="px-4 py-2 rounded-lg text-sm font-medium transition-all min-h-[44px] flex items-center justify-center flex-1 sm:flex-none"
                    style="background: #2563EB; color: white;" onmouseover="this.style.background='#1d4ed8';" onmouseout="this.style.background='#2563EB';">
                Guardar Procurador
            </button>
        </div>
    </div>

    <div class="rounded-xl p-5 mb-4" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
        <h3 class="text-sm font-semibold mb-4" style="color: #111827;">Datos personales</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Nombres (dos nombres)</label>
                <input type="text" name="procurador_nombre" value="{{ old('procurador_nombre') }}" required placeholder="Ej: Juan Carlos" class="w-full rounded-lg px-3 py-2 text-sm outline-none @error('procurador_nombre') border-red-500 @else border-gray-300 @enderror" style="color: #111827; background: #FFFFFF;">
                @error('procurador_nombre')
                <p class="text-xs mt-1 text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Apellidos (dos apellidos)</label>
                <input type="text" name="procurador_apellido" value="{{ old('procurador_apellido') }}" required placeholder="Ej: Pérez López" class="w-full rounded-lg px-3 py-2 text-sm outline-none @error('procurador_apellido') border-red-500 @else border-gray-300 @enderror" style="color: #111827; background: #FFFFFF;">
                @error('procurador_apellido')
                <p class="text-xs mt-1 text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">DNI</label>
                <input type="text" name="procurador_dni" value="{{ old('procurador_dni') }}" required placeholder="Ej: 0801-1990-01234" class="w-full rounded-lg px-3 py-2 text-sm outline-none @error('procurador_dni') border-red-500 @else border-gray-300 @enderror" style="color: #111827; background: #FFFFFF;">
                @error('procurador_dni')
                <p class="text-xs mt-1 text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Carnet profesional</label>
                <input type="text" name="procurador_carnet" value="{{ old('procurador_carnet') }}" placeholder="Ej: CAR-00123" class="w-full rounded-lg px-3 py-2 text-sm outline-none @error('procurador_carnet') border-red-500 @else border-gray-300 @enderror" style="color: #111827; background: #FFFFFF;">
                @error('procurador_carnet')
                <p class="text-xs mt-1 text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Fecha de nacimiento (DD/MM/YYYY)</label>
                <input type="text" name="procurador_fecha_nacimiento" value="{{ old('procurador_fecha_nacimiento') }}" required placeholder="Ej: 15/03/1990" class="w-full rounded-lg px-3 py-2 text-sm outline-none @error('procurador_fecha_nacimiento') border-red-500 @else border-gray-300 @enderror" style="color: #111827; background: #FFFFFF;" maxlength="10" id="fecha_nacimiento">
                @error('procurador_fecha_nacimiento')
                <p class="text-xs mt-1 text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Género</label>
                <select name="procurador_genero" class="w-full rounded-lg px-3 py-2 text-sm outline-none @error('procurador_genero') border-red-500 @else border-gray-300 @enderror" style="color: #111827; background: #FFFFFF;">
                    <option value="">Seleccionar...</option>
                    <option value="Masculino" {{ old('procurador_genero') == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                    <option value="Femenino" {{ old('procurador_genero') == 'Femenino' ? 'selected' : '' }}>Femenino</option>
                    <option value="Otro" {{ old('procurador_genero') == 'Otro' ? 'selected' : '' }}>Otro</option>
                </select>
                @error('procurador_genero')
                <p class="text-xs mt-1 text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Correo electrónico</label>
                <input type="email" name="procurador_email" value="{{ old('procurador_email') }}" required placeholder="123456789@usap.edu" class="w-full rounded-lg px-3 py-2 text-sm outline-none @error('procurador_email') border-red-500 @else border-gray-300 @enderror" style="color: #111827; background: #FFFFFF;">
                @error('procurador_email')
                <p class="text-xs mt-1 text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Teléfono / Celular</label>
                <input type="text" name="procurador_telefono" value="{{ old('procurador_telefono') }}" required placeholder="+504 1234-5678" class="w-full rounded-lg px-3 py-2 text-sm outline-none @error('procurador_telefono') border-red-500 @else border-gray-300 @enderror" style="color: #111827; background: #FFFFFF;" maxlength="15" id="telefono">
                @error('procurador_telefono')
                <p class="text-xs mt-1 text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="md:col-span-2">
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Dirección</label>
                <input type="text" name="procurador_direccion" value="{{ old('procurador_direccion') }}" placeholder="Ej: Av. Principal 123" class="w-full rounded-lg px-3 py-2 text-sm outline-none @error('procurador_direccion') border-red-500 @else border-gray-300 @enderror" style="color: #111827; background: #FFFFFF;">
                @error('procurador_direccion')
                <p class="text-xs mt-1 text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>
</form>

<script>
// Formato automático fecha DD/MM/YYYY
document.addEventListener('DOMContentLoaded', function() {
    const fechaInput = document.getElementById('fecha_nacimiento');
    if (fechaInput) {
        fechaInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) value = value.slice(0,2) + '/' + value.slice(2);
            if (value.length >= 5) value = value.slice(0,5) + '/' + value.slice(5,9);
            e.target.value = value.slice(0,10);
        });
    }

    // Formato automático teléfono +504 XXXX-XXXX
    const telefonoInput = document.getElementById('telefono');
    if (telefonoInput) {
        telefonoInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            // Si no empieza con 504, agregarlo
            if (!value.startsWith('504')) {
                value = '504' + value;
            }
            if (value.length > 3) {
                value = '+' + value.slice(0,3) + ' ' + value.slice(3,7) + (value.length > 7 ? '-' + value.slice(7,11) : '');
            } else if (value.length > 0) {
                value = '+' + value;
            }
            e.target.value = value.slice(0,16);
        });
    }
});
</script>
@endsection