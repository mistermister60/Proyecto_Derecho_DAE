@extends('layouts.app')
{{--
    Vista: procuradores/edit
    Propósito: Formulario de edición de datos del procurador. Permite modificar información personal, carnet profesional, género, email y dirección.
    Variables: $procurador (modelo Procurador)
    @extends: layouts.app
    @section: content
--}}

@section('title', 'Editar Procurador')

@section('content')
{{-- Toast de éxito --}}
@if (session('success'))
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: 'success',
        title: '¡Procurador actualizado!',
        text: '{{ session("success") }}',
        timer: 3000,
        showConfirmButton: false,
        toast: true,
        position: 'top-end',
        timerProgressBar: true,
        background: '#1E3A5F',
        color: '#fff',
        iconColor: '#FCD34D'
    });
});
</script>
@endif
<form action="{{ route('procuradores.update', $procurador->procurador_dni) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-6">
        <h1 class="text-xl font-bold" style="color: #111827;">Editar Procurador</h1>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <a href="{{ route('procuradores.show', $procurador->procurador_dni) }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-all min-h-[44px] flex items-center justify-center flex-1 sm:flex-none"
               style="background: #F3F4F6; color: #374151; border: 1px solid #E5E7EB;">
                Cancelar
            </a>
            <button type="submit" class="px-4 py-2 rounded-lg text-sm font-medium transition-all min-h-[44px] flex items-center justify-center flex-1 sm:flex-none"
                    style="background: #2563EB; color: white;" onmouseover="this.style.background='#1d4ed8';" onmouseout="this.style.background='#2563EB';">
                Guardar cambios
            </button>
        </div>
    </div>

    <div class="rounded-xl p-5 mb-4" style="background: #FFFFFF; border: 2px solid #1E3A5F;">
        <h3 class="text-sm font-semibold mb-4" style="color: #111827;">Datos personales</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Nombre(s)</label>
                <input type="text" name="procurador_nombre" value="{{ old('procurador_nombre', $procurador->procurador_nombre) }}" required placeholder="Juan Carlos" class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 2px solid #1E3A5F; color: #111827; background: #FFFFFF;">
                @error('procurador_nombre')
                <p class="text-xs mt-1 text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Apellido(s)</label>
                <input type="text" name="procurador_apellido" value="{{ old('procurador_apellido', $procurador->procurador_apellido) }}" required placeholder="Pérez Gómez" class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 2px solid #1E3A5F; color: #111827; background: #FFFFFF;">
                @error('procurador_apellido')
                <p class="text-xs mt-1 text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">DNI</label>
                <input type="text" name="procurador_dni" id="procurador_dni" value="{{ old('procurador_dni', $procurador->procurador_dni) }}" required placeholder="0801-1990-00123" maxlength="15" class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 2px solid #1E3A5F; color: #111827; background: #FFFFFF;">
                @error('procurador_dni')
                <p class="text-xs mt-1 text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Carnet profesional</label>
                <input type="text" name="procurador_carnet" value="{{ old('procurador_carnet', $procurador->procurador_carnet) }}" placeholder="CAR-00123" class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 2px solid #1E3A5F; color: #111827; background: #FFFFFF;">
                @error('procurador_carnet')
                <p class="text-xs mt-1 text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Fecha de nacimiento</label>
                <input type="date" name="procurador_fecha_nacimiento" id="procurador_fecha_nacimiento" value="{{ old('procurador_fecha_nacimiento', $procurador->procurador_fecha_nacimiento) }}" required class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 2px solid #1E3A5F; color: #111827; background: #FFFFFF;">
                @error('procurador_fecha_nacimiento')
                <p class="text-xs mt-1 text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Género</label>
                <select name="procurador_genero" required class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 2px solid #1E3A5F; color: #111827; background: #FFFFFF;">
                    <option value="">Seleccionar...</option>
                    <option value="Masculino" {{ old('procurador_genero', $procurador->procurador_genero) == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                    <option value="Femenino" {{ old('procurador_genero', $procurador->procurador_genero) == 'Femenino' ? 'selected' : '' }}>Femenino</option>
                </select>
                @error('procurador_genero')
                <p class="text-xs mt-1 text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Correo electrónico</label>
                <input type="email" name="procurador_email" id="procurador_email" value="{{ old('procurador_email', $procurador->procurador_email) }}" required placeholder="123456789@usap.edu" class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 2px solid #1E3A5F; color: #111827; background: #FFFFFF;">
                @error('procurador_email')
                <p class="text-xs mt-1 text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Teléfono / Celular</label>
                <input type="text" name="procurador_telefono" id="procurador_telefono" value="{{ old('procurador_telefono', $procurador->procurador_telefono) }}" required placeholder="+504 1234-5678" maxlength="16" class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 2px solid #1E3A5F; color: #111827; background: #FFFFFF;">
                @error('procurador_telefono')
                <p class="text-xs mt-1 text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="md:col-span-2">
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Dirección</label>
                <textarea name="procurador_direccion" rows="2" class="w-full rounded-lg px-3 py-2 text-sm outline-none resize-none" style="border: 2px solid #1E3A5F; color: #111827; background: #FFFFFF;">{{ old('procurador_direccion', $procurador->procurador_direccion) }}</textarea>
                @error('procurador_direccion')
                <p class="text-xs mt-1 text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Formato DNI: 0801-1990-00123 (auto-guiones)
    const dniInput = document.getElementById('procurador_dni');
    if (dniInput) {
        dniInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 4) value = value.slice(0,4) + '-' + value.slice(4);
            if (value.length >= 9) value = value.slice(0,9) + '-' + value.slice(9,14);
            e.target.value = value.slice(0,15);
        });
    }

    // Formato Teléfono: +504 XXXX-XXXX
    const telefonoInput = document.getElementById('procurador_telefono');
    if (telefonoInput) {
        telefonoInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
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