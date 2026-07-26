@extends('layouts.app')
{{--
    Vista: usuarios/create
    Propósito: Formulario para crear un nuevo usuario del sistema. Incluye nombre, email, contraseña, rol y procurador asociado opcional.
    Variables: $roles (Collection de modelos Rol), $procuradores (Collection de modelos Procurador)
    @extends: layouts.app
    @section: content
--}}

@section('title', 'Crear Usuario')

@section('content')
{{-- Mensajes globales de error --}}
@if ($errors->any())
<div class="mb-4 p-4 rounded-lg" style="background: #FEF2F2; border: 1px solid #FECACA;">
    <div class="flex">
        <svg class="w-5 h-5 text-red-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
        </svg>
        <div>
            <p class="text-sm font-medium text-red-800">Por favor corrige los siguientes errores:</p>
            <ul class="mt-2 list-disc list-inside text-sm text-red-700">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endif

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
                <input type="text" name="usuario_nombre" value="{{ old('usuario_nombre') }}" required placeholder="Ej: Juan Pérez" class="w-full rounded-lg px-3 py-2 text-sm outline-none @error('usuario_nombre') border-red-500 @else border-gray-300 @enderror" style="color: #111827; background: #FFFFFF;">
                @error('usuario_nombre')
                <p class="text-xs mt-1 text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Correo electrónico</label>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="ejemplo@usap.edu" class="w-full rounded-lg px-3 py-2 text-sm outline-none @error('email') border-red-500 @else border-gray-300 @enderror" style="color: #111827; background: #FFFFFF;">
                @error('email')
                <p class="text-xs mt-1 text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Contraseña</label>
                <input type="password" name="contrasena" id="contrasena" 
                       @if(old('rol_id') != 2) required @endif
                       minlength="8" 
                       placeholder="Mínimo 8 caracteres, mayúscula, minúscula, número y símbolo" 
                       class="w-full rounded-lg px-3 py-2 text-sm outline-none @error('contrasena') border-red-500 @else border-gray-300 @enderror" 
                       style="color: #111827; background: #FFFFFF;">
                @error('contrasena')
                <p class="text-xs mt-1 text-red-600">{{ $message }}</p>
                @enderror
                <p class="text-xs mt-1 text-gray-500" id="password-hint">Se genera automáticamente para Procuradores</p>
            </div>
            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Rol</label>
                <select name="rol_id" required class="w-full rounded-lg px-3 py-2 text-sm outline-none @error('rol_id') border-red-500 @else border-gray-300 @enderror" style="color: #111827; background: #FFFFFF;">
                    <option value="">Seleccionar rol...</option>
                    @foreach ($roles as $rol)
                        <option value="{{ $rol->rol_id }}" {{ old('rol_id') == $rol->rol_id ? 'selected' : '' }}>{{ $rol->rol_nombre }}</option>
                    @endforeach
                </select>
                @error('rol_id')
                <p class="text-xs mt-1 text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Procurador asociado (opcional)</label>
                <select name="procurador_id" class="w-full rounded-lg px-3 py-2 text-sm outline-none @error('procurador_id') border-red-500 @else border-gray-300 @enderror" style="color: #111827; background: #FFFFFF;">
                    <option value="">Sin procurador...</option>
                    @foreach ($procuradores as $procurador)
                        <option value="{{ $procurador->procurador_id }}" {{ old('procurador_id') == $procurador->procurador_id ? 'selected' : '' }}>{{ $procurador->nombre_completo }}</option>
                    @endforeach
                </select>
                @error('procurador_id')
                <p class="text-xs mt-1 text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const rolSelect = document.querySelector('select[name="rol_id"]');
    const passwordField = document.getElementById('contrasena');
    const passwordHint = document.getElementById('password-hint');
    
    if (!rolSelect || !passwordField || !passwordHint) return;
    
    function togglePasswordField() {
        const isProcurador = rolSelect.value === '2'; // rol_id 2 = Procurador
        
        if (isProcurador) {
            passwordField.removeAttribute('required');
            passwordField.disabled = true;
            passwordField.placeholder = 'Se genera automáticamente y se envía por email';
            passwordField.style.backgroundColor = '#F3F4F6';
            passwordHint.style.display = 'block';
        } else {
            passwordField.setAttribute('required', 'required');
            passwordField.disabled = false;
            passwordField.placeholder = 'Mínimo 8 caracteres, mayúscula, minúscula, número y símbolo';
            passwordField.style.backgroundColor = '#FFFFFF';
            passwordHint.style.display = 'none';
        }
    }
    
    // Ejecutar al cargar
    togglePasswordField();
    
    // Ejecutar al cambiar rol
    rolSelect.addEventListener('change', togglePasswordField);
});
</script>
@endsection
