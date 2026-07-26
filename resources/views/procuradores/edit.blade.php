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
<form action="{{ route('procuradores.update', $procurador->procurador_dni) }}" method="POST" enctype="multipart/form-data">
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

    {{-- Sección 1: Datos Personales e Identificación --}}
    <div class="rounded-xl p-5 mb-4" style="background: #FFFFFF; border: 2px solid #1E3A5F;">
        <h3 class="text-sm font-semibold mb-4" style="color: #111827;">Datos Personales e Identificación</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Nombres</label>
                <input type="text" name="procurador_nombre" value="{{ old('procurador_nombre', $procurador->procurador_nombre) }}" required placeholder="Juan Carlos" class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 2px solid #1E3A5F; color: #111827; background: #FFFFFF;">
                @error('procurador_nombre')
                <p class="text-xs mt-1 text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Apellidos</label>
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
            <div class="sm:col-span-2 flex items-center justify-center">
                <div>
                    <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Foto de perfil</label>
                    <div class="relative">
                        <div id="foto-preview" class="w-full rounded-lg border-2 border-dashed #1E3A5F flex items-center justify-center bg-gray-50 p-2" style="background: #F9FAFB; color: #6B7280; min-height: 44px; max-height: 44px;">
                            @if ($procurador->procurador_foto)
                                <img src="{{ asset('storage/' . $procurador->procurador_foto) }}" alt="Foto actual" class="w-full h-full object-cover rounded-lg">
                            @else
                                <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v16.5m9-9h16.5m-16.5-9h16.5m0 9v9M3.75 21h16.5a2.25 2.25 0 002.25-2.25V6.75a2.25 2.25 0 00-2.25-2.25H3.75A2.25 2.25 0 001.5 6.75v12.75A2.25 2.25 0 003.75 21z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 15a3 3 0 00-2.83-2.993 2.5 2.5 0 00-2.83 0A3 3 0 003 21h18a3 3 0 00-2.83-5.993 2.5 2.5 0 00-2.83 0A3 3 0 003 21h18z" />
                                </svg>
                            @endif
                            <input type="file" name="procurador_foto" id="procurador_foto" accept="image/jpeg,image/png,image/jpg" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" style="max-width: 100%;">
                        </div>
                    </div>
                    @error('procurador_foto')
                    <p class="text-xs mt-1 text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    {{-- Sección 2: Información de Contacto --}}
    <div class="rounded-xl p-5 mb-4" style="background: #FFFFFF; border: 2px solid #1E3A5F;">
        <h3 class="text-sm font-semibold mb-4" style="color: #111827;">Información de Contacto</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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
        </div>
        <div class="mt-4">
            <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Dirección</label>
            <textarea name="procurador_direccion" rows="2" class="w-full rounded-lg px-3 py-2 text-sm outline-none resize-none" style="border: 2px solid #1E3A5F; color: #111827; background: #FFFFFF;">{{ old('procurador_direccion', $procurador->procurador_direccion) }}</textarea>
            @error('procurador_direccion')
            <p class="text-xs mt-1 text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Sección 3: Información Profesional --}}
    <div class="rounded-xl p-5 mb-4" style="background: #FFFFFF; border: 2px solid #1E3A5F;">
        <h3 class="text-sm font-semibold mb-4" style="color: #111827;">Información Profesional</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Carnet profesional</label>
                <input type="text" name="procurador_carnet" value="{{ old('procurador_carnet', $procurador->procurador_carnet) }}" placeholder="CAR-00123" class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 2px solid #1E3A5F; color: #111827; background: #FFFFFF;">
                @error('procurador_carnet')
                <p class="text-xs mt-1 text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Estado</label>
                <select name="procurador_estado" class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 2px solid #1E3A5F; color: #111827; background: #FFFFFF;">
                    <option value="activo" {{ old('procurador_estado', $procurador->procurador_estado) == 'activo' ? 'selected' : '' }}>Activo</option>
                    <option value="inactivo" {{ old('procurador_estado', $procurador->procurador_estado) == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                </select>
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

    // Preview de foto
    const fotoInput = document.getElementById('procurador_foto');
    const fotoPreview = document.getElementById('foto-preview');
    if (fotoInput && fotoPreview) {
        fotoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    fotoPreview.innerHTML = `<img src="${e.target.result}" alt="Vista previa" class="w-full h-full object-cover rounded-lg">`;
                };
                reader.readAsDataURL(file);
            }
        });
    }
});
</script>
@endsection