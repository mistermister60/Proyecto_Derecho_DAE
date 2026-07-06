@extends('layouts.app')
{{--
    Vista: casos/create
    Propósito: Formulario para crear un nuevo caso. Incluye selección de cliente, tipo de trámite, parte representada, procurador asignado, juzgado y relación de hechos.
    Variables: $clientes (Collection de modelos Cliente activos), $procuradores (Collection de modelos Procurador activos), $tramites (Collection de tipos de trámite activos)
    @extends: layouts.app
    @section: content
--}}

@section('title', 'Nuevo caso')

@section('content')
<form action="{{ route('casos.store') }}" method="POST">
    @csrf
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-3">
        <h1 class="text-xl font-bold" style="color: #111827;">Nuevo caso</h1>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <a href="{{ route('casos.index') }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-all min-h-[44px] inline-flex items-center justify-center flex-1 sm:flex-none"
               style="background: #F3F4F6; color: #374151; border: 1px solid #E5E7EB;">
                Cancelar
            </a>
            <button type="submit" class="px-4 py-2 rounded-lg text-sm font-medium transition-all min-h-[44px] flex-1 sm:flex-none"
                    style="background: #2563EB; color: white;" onmouseover="this.style.background='#1d4ed8';" onmouseout="this.style.background='#2563EB';">
                Guardar caso
            </button>
        </div>
    </div>

    {{-- Sección: Cliente --}}
    <div class="rounded-xl p-5 mb-4" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
        <h3 class="text-sm font-semibold mb-4" style="color: #111827;">Cliente</h3>
        <select name="cliente_id" required class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;">
            <option value="">Seleccionar cliente...</option>
            @foreach ($clientes as $cliente)
            <option value="{{ $cliente->cliente_id }}" {{ old('cliente_id') == $cliente->cliente_id ? 'selected' : '' }}>
                {{ $cliente->nombre_completo }} — {{ $cliente->cliente_dni }}
            </option>
            @endforeach
        </select>
        @error('cliente_id')
        <p class="text-xs mt-1" style="color: #DC2626;">{{ $message }}</p>
        @enderror
    </div>

    {{-- Sección: Tipo de trámite --}}
    <div class="rounded-xl p-5 mb-4" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
        <h3 class="text-sm font-semibold mb-4" style="color: #111827;">Tipo de trámite</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Tipo de trámite</label>
                <select name="tipo_tramite_id" required class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;">
                    <option value="">Seleccionar...</option>
                    @foreach ($tramites as $tramite)
                    <option value="{{ $tramite->tipo_tramite_id }}" {{ old('tipo_tramite_id') == $tramite->tipo_tramite_id ? 'selected' : '' }}>
                        {{ $tramite->tramite_nombre }}
                    </option>
                    @endforeach
                </select>
                @error('tipo_tramite_id')
                <p class="text-xs mt-1" style="color: #DC2626;">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Parte representada</label>
                <select name="caso_parte_representada" required class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;">
                    <option value="">Seleccionar...</option>
                    <option value="Demandante" {{ old('caso_parte_representada') === 'Demandante' ? 'selected' : '' }}>Demandante</option>
                    <option value="Demandado" {{ old('caso_parte_representada') === 'Demandado' ? 'selected' : '' }}>Demandado</option>
                    <option value="Ambas partes" {{ old('caso_parte_representada') === 'Ambas partes' ? 'selected' : '' }}>Ambas partes</option>
                </select>
                @error('caso_parte_representada')
                <p class="text-xs mt-1" style="color: #DC2626;">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    {{-- Sección: Asignación --}}
    <div class="rounded-xl p-5 mb-4" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
        <h3 class="text-sm font-semibold mb-4" style="color: #111827;">Asignación</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Procurador asignado</label>
                <select name="procurador_id" required class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;">
                    <option value="">Seleccionar procurador...</option>
                    @foreach ($procuradores as $procurador)
                    <option value="{{ $procurador->procurador_id }}" {{ old('procurador_id') == $procurador->procurador_id ? 'selected' : '' }}>
                        {{ $procurador->nombre_completo }} — Carnet {{ $procurador->procurador_carnet }}
                    </option>
                    @endforeach
                </select>
                @error('procurador_id')
                <p class="text-xs mt-1" style="color: #DC2626;">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Juzgado (opcional)</label>
                <input type="text" name="caso_juzgado" value="{{ old('caso_juzgado') }}" placeholder="Ej: J-7" class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;">
            </div>
        </div>
    </div>

    {{-- Sección: Hechos --}}
    <div class="rounded-xl p-5 mb-4" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
        <h3 class="text-sm font-semibold mb-4" style="color: #111827;">Relación de hechos</h3>
        <div>
            <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Narración de los hechos</label>
            <textarea name="caso_relacion_hechos" rows="6" required class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;" placeholder="Describa detalladamente los hechos del caso...">{{ old('caso_relacion_hechos') }}</textarea>
            @error('caso_relacion_hechos')
            <p class="text-xs mt-1" style="color: #DC2626;">{{ $message }}</p>
            @enderror
        </div>
        <div class="mt-4">
            <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Observaciones del Director (opcional)</label>
            <textarea name="caso_observaciones_director" rows="3" class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;" placeholder="Notas internas...">{{ old('caso_observaciones_director') }}</textarea>
        </div>
    </div>
</form>
@endsection
