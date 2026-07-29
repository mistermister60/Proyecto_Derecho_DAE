@extends('layouts.app')

@section('title', 'Mi Perfil')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    {{-- Header --}}
    <div class="flex items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold" style="color: #111827;">Mi Perfil</h1>
            <p class="text-sm mt-1" style="color: #6B7280;">Actualiza tu información personal y preferencias</p>
        </div>
    </div>

    {{-- Status messages --}}
    @if (session('success'))
        <div style="background: #D1FAE5; border: 1px solid #10B981; color: #065F46; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
            {{ session('status') }}
        </div>
    @endif
    @if (session('success'))
        <div style="background: #D1FAE5; border: 1px solid #10B981; color: #065F46; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div style="background: #FEE2E2; border: 1px solid #EF4444; color: #991B1B; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Información de perfil --}}
        <div class="lg:col-span-2 space-y-6">
            <div style="background: white; border: 1px solid #E5E7EB; border-radius: 12px; padding: 24px;">
                <h2 class="text-lg font-semibold mb-4" style="color: #111827;">Información de Perfil</h2>
                
                <form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
                    @csrf
                    @method('PATCH')
                    
                    <div>
                        <label for="usuario_nombre" style="display: block; color: #374151; font-size: 14px; font-weight: 600; margin-bottom: 6px;">Nombre Completo</label>
                        <input type="text" name="usuario_nombre" id="usuario_nombre" required value="{{ old('usuario_nombre', auth()->user()->usuario_nombre) }}"
                               style="width: 100%; padding: 12px; font-size: 15px; border: 2px solid #E5E7EB; border-radius: 8px; box-sizing: border-box;"
                               autocomplete="name">
                        @error('usuario_nombre')
                            <p style="color: #DC2626; font-size: 13px; margin-top: 4px;">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" style="display: block; color: #374151; font-size: 14px; font-weight: 600; margin-bottom: 6px;">Correo Electrónico</label>
                        <input type="email" name="email" id="email" required value="{{ old('email', auth()->user()->email) }}"
                               style="width: 100%; padding: 12px; font-size: 15px; border: 2px solid #E5E7EB; border-radius: 8px; box-sizing: border-box;"
                               autocomplete="email">
                        @error('email')
                            <p style="color: #DC2626; font-size: 13px; margin-top: 4px;">{{ $message }}</p>
                        @enderror
                    </div>

                    <div style="display: flex; flex-wrap: wrap; gap: 12px; margin-top: 8px;">
                        <button type="submit" style="background: #1E3A8A; color: white; padding: 12px 24px; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer;">
                            Guardar Cambios
                        </button>
                        @if (session('success') === 'Perfil actualizado exitosamente.')
                            <div style="display: flex; align-items: center; padding: 8px 16px; background: #D1FAE5; color: #065F46; border-radius: 8px; font-size: 14px;">
                                Perfil actualizado
                            </div>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Cambiar contraseña --}}
            <div style="background: white; border: 1px solid #E5E7EB; border-radius: 12px; padding: 24px;">
                <h2 class="text-lg font-semibold mb-4" style="color: #111827;">Cambiar Contraseña</h2>
                
                <form method="POST" action="{{ route('profile.password.update') }}" class="space-y-5">
                    @csrf
                    @method('PUT')
                    
                    <div>
                        <label for="current_password" style="display: block; color: #374151; font-size: 14px; font-weight: 600; margin-bottom: 6px;">Contraseña Actual</label>
                        <input type="password" name="current_password" id="current_password" required autocomplete="current-password"
                               style="width: 100%; padding: 12px; font-size: 15px; border: 2px solid #E5E7EB; border-radius: 8px; box-sizing: border-box;">
                        @error('current_password')
                            <p style="color: #DC2626; font-size: 13px; margin-top: 4px;">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" style="display: block; color: #374151; font-size: 14px; font-weight: 600; margin-bottom: 6px;">Nueva Contraseña</label>
                        <input type="password" name="password" id="password" required autocomplete="new-password"
                               style="width: 100%; padding: 12px; font-size: 15px; border: 2px solid #E5E7EB; border-radius: 8px; box-sizing: border-box;">
                        <p style="color: #9CA3AF; font-size: 12px; margin-top: 6px;">Mínimo 8 caracteres, mayúsculas, minúsculas, números y símbolos.</p>
                        @error('password')
                            <p style="color: #DC2626; font-size: 13px; margin-top: 4px;">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" style="display: block; color: #374151; font-size: 14px; font-weight: 600; margin-bottom: 6px;">Confirmar Nueva Contraseña</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password"
                               style="width: 100%; padding: 12px; font-size: 15px; border: 2px solid #E5E7EB; border-radius: 8px; box-sizing: border-box;">
                        @error('password_confirmation')
                            <p style="color: #DC2626; font-size: 13px; margin-top: 4px;">{{ $message }}</p>
                        @enderror
                    </div>

                    <div style="display: flex; flex-wrap: wrap; gap: 12px; margin-top: 8px;">
                        <button type="submit" style="background: #1E3A8A; color: white; padding: 12px 24px; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer;">
                            Actualizar Contraseña
                        </button>
                        @if (session('status') === 'password-updated')
                            <div style="display: flex; align-items: center; padding: 8px 16px; background: #D1FAE5; color: #065F46; border-radius: 8px; font-size: 14px;">
                                Contraseña actualizada
                            </div>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Eliminar cuenta --}}
            <div style="background: white; border: 1px solid #E5E7EB; border-radius: 12px; padding: 24px;">
                <h2 class="text-lg font-semibold mb-4" style="color: #111827;">Eliminar Cuenta</h2>
                
                <p class="text-sm mb-4" style="color: #6B7280;">Una vez que tu cuenta sea eliminada, todos sus recursos y datos se eliminarán permanentemente. Antes de eliminar tu cuenta, descarga cualquier dato o información que desees conservar.</p>
                
                <button type="button" onclick="openDeleteModal()" style="background: #FEE2E2; color: #DC2626; border: 1px solid #FECACA; padding: 12px 24px; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer;"
                       onmouseover="this.style.background='#FECACA';" onmouseout="this.style.background='#FEE2E2';">
                    Eliminar Cuenta
                </button>
            </div>
        </div>

        {{-- Información del usuario --}}
        <div class="space-y-6">
            <div style="background: white; border: 1px solid #E5E7EB; border-radius: 12px; padding: 24px; text-align: center;">
                <div style="width: 80px; height: 80px; border-radius: 50%; background: #1E3A5F; color: white; display: flex; align-items: center; justify-content: center; font-size: 28px; font-weight: 600; margin: 0 auto 16px;">
                    {{ strtoupper(substr(explode(' ', auth()->user()->usuario_nombre)[0] ?? 'U', 0, 1)) }}{{ strtoupper(substr(explode(' ', auth()->user()->usuario_nombre)[1] ?? '', 0, 1)) }}
                </div>
                <h3 class="text-lg font-semibold" style="color: #111827;">{{ auth()->user()->usuario_nombre }}</h3>
                <p class="text-sm mt-1" style="color: #6B7280;">{{ auth()->user()->email }}</p>
                
                <div class="mt-4 pt-4 border-t" style="border-color: #E5E7EB;">
                    <p class="text-xs" style="color: #9CA3AF;">Rol</p>
                    <p class="font-medium" style="color: #111827;">{{ auth()->user()->rol?->rol_nombre ?? '—' }}</p>
                </div>
                
                @if (auth()->user()->procurador)
                <div class="mt-4 pt-4 border-t" style="border-color: #E5E7EB;">
                    <p class="text-xs" style="color: #9CA3AF;">Procurador Asociado</p>
                    <p class="font-medium" style="color: #111827;">{{ auth()->user()->procurador->nombre_completo }}</p>
                    <p class="text-sm mt-1" style="color: #6B7280;">{{ auth()->user()->procurador->procurador_email }}</p>
                </div>
                @endif
                
                <div class="mt-4 pt-4 border-t" style="border-color: #E5E7EB;">
                    <p class="text-xs" style="color: #9CA3AF;">Miembro desde</p>
                    <p class="font-medium" style="color: #111827;">{{ auth()->user()->created_at ? auth()->user()->created_at->format('d/m/Y') : '—' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Eliminar Cuenta --}}
<div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center" style="background: rgba(0,0,0,0.5);">
    <div style="background: white; border-radius: 12px; padding: 24px; width: 100%; max-width: 450px; margin: 16px;">
        <h2 class="text-lg font-semibold mb-4" style="color: #111827;">¿Estás seguro de que quieres eliminar tu cuenta?</h2>
        
        <p class="text-sm mb-6" style="color: #6B7280;">Una vez eliminada tu cuenta, todos sus recursos y datos se eliminarán permanentemente. Ingresa tu contraseña para confirmar.</p>
        
        <form method="POST" action="{{ route('profile.destroy') }}" class="space-y-4">
            @csrf
            @method('DELETE')
            
            <div>
                <label for="delete_password" style="display: block; color: #374151; font-size: 14px; font-weight: 600; margin-bottom: 6px;">Contraseña</label>
                <input type="password" name="password" id="delete_password" required autocomplete="current-password"
                       style="width: 100%; padding: 12px; font-size: 15px; border: 2px solid #E5E7EB; border-radius: 8px; box-sizing: border-box;"
                       placeholder="Ingresa tu contraseña">
                @error('password')
                    <p style="color: #DC2626; font-size: 13px; margin-top: 4px;">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="flex gap-3 justify-end">
                <button type="button" onclick="closeDeleteModal()" style="background: #F3F4F6; color: #374151; border: 1px solid #E5E7EB; padding: 12px 24px; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer;">
                    Cancelar
                </button>
                <button type="submit" style="background: #DC2626; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer;"
                       onmouseover="this.style.background='#B91C1C';" onmouseout="this.style.background='#DC2626';">
                    Eliminar Cuenta
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openDeleteModal() {
        document.getElementById('deleteModal').classList.remove('hidden');
        document.getElementById('deleteModal').classList.add('flex');
    }
    
    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        document.getElementById('deleteModal').classList.remove('flex');
    }
    
    // Cerrar modal al hacer click fuera
    document.getElementById('deleteModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeDeleteModal();
    });
</script>
@endsection