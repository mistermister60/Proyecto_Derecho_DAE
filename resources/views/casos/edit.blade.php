@extends('layouts.app')

@section('title', 'Editar Caso — ' . $caso->caso_numero_expediente)

@section('content')
<form action="{{ route('casos.update', $caso->caso_numero_expediente) }}" method="POST">
    @csrf
    @method('PUT')
    
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold" style="color: #111827;">Editar caso</h1>
            <p class="text-xs font-semibold mt-1" style="color: #6B7280;">
                Expediente: <span class="font-mono bg-gray-100 px-1.5 py-0.5 rounded border border-gray-200" style="color: #111827;">{{ $caso->caso_numero_expediente }}</span>
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('casos.show', $caso->caso_numero_expediente) }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-all"
               style="background: #F3F4F6; color: #374151; border: 1px solid #E5E7EB;">
                Cancelar
            </a>
            <button type="submit" class="px-4 py-2 rounded-lg text-sm font-medium transition-all"
                    style="background: #2563EB; color: white;" onmouseover="this.style.background='#1d4ed8';" onmouseout="this.style.background='#2563EB';">
                Guardar cambios
            </button>
        </div>
    </div>

    {{-- Sección: Partes Involucradas --}}
    <div class="rounded-xl p-5 mb-4" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
        <h3 class="text-sm font-semibold mb-4" style="color: #111827;">Partes Involucradas</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Cliente (Representado)</label>
                <select name="cliente_id" required class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;">
                    <option value="">Seleccionar cliente...</option>
                    @foreach ($clientes as $cliente)
                    <option value="{{ $cliente->cliente_id }}" {{ old('cliente_id', $caso->cliente_id) == $cliente->cliente_id ? 'selected' : '' }}>
                        {{ $cliente->nombre_completo }} — {{ $cliente->cliente_dni }}
                    </option>
                    @endforeach
                </select>
                @error('cliente_id')
                <p class="text-xs mt-1" style="color: #DC2626;">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Demandado (Opcional)</label>
                <select name="demandado_id" class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;">
                    <option value="">Seleccionar demandado (si aplica)...</option>
                    @foreach ($demandados as $dem)
                    <option value="{{ $dem->demandado_id }}" {{ old('demandado_id', $caso->demandado_id) == $dem->demandado_id ? 'selected' : '' }}>
                        {{ $dem->demandado_nombre ?? $dem->nombre_completo }}
                    </option>
                    @endforeach
                </select>
                @error('demandado_id')
                <p class="text-xs mt-1" style="color: #DC2626;">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    {{-- Sección: Información del Trámite, Estado y Flujo --}}
    <div class="rounded-xl p-5 mb-4" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
        <h3 class="text-sm font-semibold mb-4" style="color: #111827;">Detalles Legales del Trámite</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Tipo de trámite</label>
                <select name="tipo_tramite_id" required class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;">
                    <option value="">Seleccionar...</option>
                    @foreach ($tramites as $tramite)
                    <option value="{{ $tramite->tipo_tramite_id }}" {{ old('tipo_tramite_id', $caso->tipo_tramite_id) == $tramite->tipo_tramite_id ? 'selected' : '' }}>
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
                    <option value="Demandante" {{ old('caso_parte_representada', $caso->caso_parte_representada) === 'Demandante' ? 'selected' : '' }}>Demandante</option>
                    <option value="Demandado" {{ old('caso_parte_representada', $caso->caso_parte_representada) === 'Demandado' ? 'selected' : '' }}>Demandado</option>
                    <option value="Ambas partes" {{ old('caso_parte_representada', $caso->caso_parte_representada) === 'Ambas partes' ? 'selected' : '' }}>Ambas partes</option>
                </select>
                @error('caso_parte_representada')
                <p class="text-xs mt-1" style="color: #DC2626;">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Estado de la Línea (Pipeline)</label>
                <select name="estado_id" required class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;">
                    <option value="">Seleccionar estado...</option>
                    @foreach ($estados as $estado)
                    <option value="{{ $estado->estado_id }}" {{ old('estado_id', $caso->estado_id) == $estado->estado_id ? 'selected' : '' }}>
                        {{ $estado->estado_nombre }}
                    </option>
                    @endforeach
                </select>
                @error('estado_id')
                <p class="text-xs mt-1" style="color: #DC2626;">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Estado del Registro</label>
                <select name="caso_estado" required class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;">
                    <option value="activo" {{ old('caso_estado', $caso->caso_estado) === 'activo' ? 'selected' : '' }}>Activo</option>
                    <option value="cerrado" {{ old('caso_estado', $caso->caso_estado) === 'cerrado' ? 'selected' : '' }}>Cerrado</option>
                </select>
                @error('caso_estado')
                <p class="text-xs mt-1" style="color: #DC2626;">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    {{-- Sección: Asignación y Fechas --}}
    <div class="rounded-xl p-5 mb-4" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
        <h3 class="text-sm font-semibold mb-4" style="color: #111827;">Asignación Judicial y Tiempos</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Procurador asignado</label>
                <select name="procurador_id" required class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;">
                    <option value="">Seleccionar procurador...</option>
                    @foreach ($procuradores as $procurador)
                    <option value="{{ $procurador->procurador_id }}" {{ old('procurador_id', $caso->procurador_id) == $procurador->procurador_id ? 'selected' : '' }}>
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
                <input type="text" name="caso_juzgado" value="{{ old('caso_juzgado', $caso->caso_juzgado) }}" placeholder="Ej: J-7" class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;">
                @error('caso_juzgado')
                <p class="text-xs mt-1" style="color: #DC2626;">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Fecha de Interposición</label>
                <input type="date" name="caso_fecha_interpuesta" value="{{ old('caso_fecha_interpuesta', $caso->caso_fecha_interpuesta ? \Carbon\Carbon::parse($caso->caso_fecha_interpuesta)->format('Y-m-d') : '') }}" class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;">
                @error('caso_fecha_interpuesta')
                <p class="text-xs mt-1" style="color: #DC2626;">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    {{-- Sección: Hechos --}}
    <div class="rounded-xl p-5 mb-4" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
        <h3 class="text-sm font-semibold mb-4" style="color: #111827;">Relación de hechos</h3>
        <div>
            <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Narración de los hechos</label>
            <textarea name="caso_relacion_hechos" rows="6" required class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;" placeholder="Describa detalladamente los hechos del caso...">{{ old('caso_relacion_hechos', $caso->caso_relacion_hechos) }}</textarea>
            @error('caso_relacion_hechos')
            <p class="text-xs mt-1" style="color: #DC2626;">{{ $message }}</p>
            @enderror
        </div>
        <div class="mt-4">
            <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Observaciones del Director (opcional)</label>
            <textarea name="caso_observaciones_director" rows="3" class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;" placeholder="Notas internas...">{{ old('caso_observaciones_director', $caso->caso_observaciones_director) }}</textarea>
            @error('caso_observaciones_director')
            <p class="text-xs mt-1" style="color: #DC2626;">{{ $message }}</p>
            @enderror
        </div>
    </div>
</form>
@endsection