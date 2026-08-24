@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8">
    <div class="bg-white dark:bg-zinc-800 shadow-sm rounded-lg p-6">
        <h1 class="text-xl font-semibold text-zinc-800 dark:text-zinc-100 mb-2">
            Completa tu perfil
        </h1>
        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-6">
            Antes de continuar, debes registrar tu información demográfica. Los campos
            marcados con <span class="text-red-600">*</span> son obligatorios.
        </p>

        @if (session('warning'))
            <div class="mb-4 rounded-md bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 px-4 py-2 text-sm text-amber-700 dark:text-amber-300">
                {{ session('warning') }}
            </div>
        @endif

        <form method="POST" action="{{ route('procuradores.completar-perfil.store') }}" class="space-y-5">
            @csrf

            <div>
                <label for="procurador_dni" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                    DNI (Cédula) <span class="text-red-600">*</span>
                </label>
                <input type="text" name="procurador_dni" id="procurador_dni"
                    class="w-full rounded-md border-zinc-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 focus:border-blue-500 focus:ring-blue-500"
                    placeholder="Ej. 0801-2020-XXXXX" value="{{ old('procurador_dni') }}">
                @error('procurador_dni')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="procurador_fecha_nacimiento" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                    Fecha de nacimiento <span class="text-red-600">*</span>
                </label>
                <input type="date" name="procurador_fecha_nacimiento" id="procurador_fecha_nacimiento"
                    class="w-full rounded-md border-zinc-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 focus:border-blue-500 focus:ring-blue-500"
                    value="{{ old('procurador_fecha_nacimiento') }}">
                @error('procurador_fecha_nacimiento')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="procurador_telefono" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                    Número de celular actual <span class="text-red-600">*</span>
                </label>
                <input type="text" name="procurador_telefono" id="procurador_telefono"
                    class="w-full rounded-md border-zinc-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 focus:border-blue-500 focus:ring-blue-500"
                    placeholder="Ej. 9876-5432" value="{{ old('procurador_telefono') }}">
                @error('procurador_telefono')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="procurador_contacto_emergencia" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                    Contacto de emergencia <span class="text-red-600">*</span>
                </label>
                <input type="text" name="procurador_contacto_emergencia" id="procurador_contacto_emergencia"
                    class="w-full rounded-md border-zinc-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 focus:border-blue-500 focus:ring-blue-500"
                    placeholder="Nombre y teléfono" value="{{ old('procurador_contacto_emergencia') }}">
                @error('procurador_contacto_emergencia')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="procurador_genero" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                    Género
                </label>
                <select name="procurador_genero" id="procurador_genero"
                    class="w-full rounded-md border-zinc-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 focus:border-blue-500 focus:ring-blue-500">
                    <option value="" {{ old('procurador_genero') ? '' : 'selected' }}>Sin especificar</option>
                    <option value="Masculino" {{ old('procurador_genero') === 'Masculino' ? 'selected' : '' }}>Masculino</option>
                    <option value="Femenino" {{ old('procurador_genero') === 'Femenino' ? 'selected' : '' }}>Femenino</option>
                </select>
                @error('procurador_genero')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="procurador_direccion" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                    Dirección
                </label>
                <textarea name="procurador_direccion" id="procurador_direccion" rows="2"
                    class="w-full rounded-md border-zinc-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 focus:border-blue-500 focus:ring-blue-500"
                    placeholder="Dirección de residencia">{{ old('procurador_direccion') }}</textarea>
                @error('procurador_direccion')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <button type="submit"
                    class="inline-flex items-center justify-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Guardar y continuar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
