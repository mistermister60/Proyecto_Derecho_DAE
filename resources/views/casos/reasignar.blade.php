@extends('layouts.app')

@section('title', 'Reasignar caso')

@section('content')
<form action="{{ route('casos.storeReasignacion', $caso->caso_numero_expediente) }}" method="POST">
    @csrf
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold" style="color: #111827;">Reasignar caso</h1>
            <p class="text-sm mt-1" style="color: #6B7280;">Expediente {{ $caso->caso_numero_expediente }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('casos.show', $caso->caso_numero_expediente) }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-all"
               style="background: #F3F4F6; color: #374151; border: 1px solid #E5E7EB;">
                Cancelar
            </a>
            <button type="submit" class="px-4 py-2 rounded-lg text-sm font-medium transition-all"
                    style="background: #A855F7; color: white;" onmouseover="this.style.background='#9333EA';" onmouseout="this.style.background='#A855F7';">
                Confirmar reasignación
            </button>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-6">
        {{-- Información actual --}}
        <div class="rounded-xl p-5" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
            <h3 class="text-sm font-semibold mb-4" style="color: #111827;">Información actual</h3>
            <dl class="space-y-3">
                <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">Expediente</dt><dd class="text-sm font-medium" style="color: #111827;">{{ $caso->caso_numero_expediente }}</dd></div>
                <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">Cliente</dt><dd class="text-sm font-medium" style="color: #111827;">{{ $caso->cliente?->nombre_completo ?? 'N/A' }}</dd></div>
                <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">Procurador actual</dt><dd class="text-sm font-medium" style="color: #111827;">{{ $caso->procurador?->nombre_completo ?? 'No asignado' }}</dd></div>
            </dl>
        </div>

        {{-- Nueva asignación --}}
        <div class="col-span-2 rounded-xl p-5" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
            <h3 class="text-sm font-semibold mb-4" style="color: #111827;">Nuevo procurador</h3>

            <input type="hidden" name="procurador_origen_id" value="{{ $caso->procurador_id }}">

            <div class="space-y-4">
                @foreach ($procuradores as $procurador)
                <label class="flex items-center gap-4 p-4 rounded-lg transition-all cursor-pointer" style="border: 1px solid #E5E7EB;" onmouseover="this.style.borderColor='#93C5FD'; this.style.background='#F9FAFB';" onmouseout="this.style.borderColor='#E5E7EB'; this.style.background='transparent';">
                    <input type="radio" name="procurador_destino_id" value="{{ $procurador->procurador_id }}" class="accent-blue-600" required>
                    <div class="flex items-center justify-center rounded-full shrink-0" style="width: 40px; height: 40px; background: #1E3A5F; color: white; font-size: 14px; font-weight: 600;">
                        {{ strtoupper(substr($procurador->procurador_nombre, 0, 1)) }}{{ strtoupper(substr($procurador->procurador_apellido, 0, 1)) }}
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium" style="color: #111827;">{{ $procurador->nombre_completo }}</p>
                        <p class="text-xs" style="color: #6B7280;">Carnet {{ $procurador->procurador_carnet }} • {{ $procurador->procurador_email }}</p>
                    </div>
                </label>
                @endforeach
            </div>
            @error('procurador_destino_id')
            <p class="text-xs mt-2" style="color: #DC2626;">{{ $message }}</p>
            @enderror

            <div class="mt-4">
                <label class="text-xs font-medium mb-1.5 block" style="color: #6B7280;">Motivo de la reasignación</label>
                <textarea name="reasignacion_motivo" rows="3" required class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;" placeholder="Explique el motivo del cambio de procurador...">{{ old('reasignacion_motivo') }}</textarea>
                @error('reasignacion_motivo')
                <p class="text-xs mt-1" style="color: #DC2626;">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>
</form>
@endsection
