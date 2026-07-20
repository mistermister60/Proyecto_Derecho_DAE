@extends('layouts.app')

@section('title', "Cerrar {$caso->caso_numero_expediente}")

@section('content')
<div class="max-w-2xl mx-auto">
    <h1 class="text-lg md:text-xl font-bold mb-2" style="color: #111827;">Cerrar caso</h1>
    <p class="text-sm mb-6" style="color: #6B7280;">
        Expediente {{ $caso->caso_numero_expediente }} — {{ $caso->cliente?->nombre_completo ?? 'Sin cliente' }}
    </p>

    <div class="rounded-xl p-6" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
        <form action="{{ route('casos.storeCerrar', $caso->caso_numero_expediente) }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="text-sm font-medium mb-2 block" style="color: #374151;">Tipo de resolución</label>
                <p class="text-xs mb-3" style="color: #9CA3AF;">Selecciona cómo se resolvió el caso</p>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    <label class="flex items-center gap-3 p-4 rounded-lg cursor-pointer transition-all duration-150"
                           style="border: 2px solid #E5E7EB; background: #FAFAFA;"
                           onmouseover="this.style.borderColor='#2563EB'"
                           onmouseout="this.style.borderColor='#E5E7EB'">
                        <input type="radio" name="resolucion_tipo" value="ganado" required class="w-4 h-4 accent-blue-600">
                        <div>
                            <p class="text-sm font-medium" style="color: #111827;">Ganado</p>
                            <p class="text-xs" style="color: #6B7280;">Sentencia favorable</p>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-4 rounded-lg cursor-pointer transition-all duration-150"
                           style="border: 2px solid #E5E7EB; background: #FAFAFA;"
                           onmouseover="this.style.borderColor='#DC2626'"
                           onmouseout="this.style.borderColor='#E5E7EB'">
                        <input type="radio" name="resolucion_tipo" value="perdido" required class="w-4 h-4 accent-red-600">
                        <div>
                            <p class="text-sm font-medium" style="color: #111827;">Perdido</p>
                            <p class="text-xs" style="color: #6B7280;">Sentencia desfavorable</p>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-4 rounded-lg cursor-pointer transition-all duration-150"
                           style="border: 2px solid #E5E7EB; background: #FAFAFA;"
                           onmouseover="this.style.borderColor='#16A34A'"
                           onmouseout="this.style.borderColor='#E5E7EB'">
                        <input type="radio" name="resolucion_tipo" value="conciliado" required class="w-4 h-4 accent-green-600">
                        <div>
                            <p class="text-sm font-medium" style="color: #111827;">Conciliado</p>
                            <p class="text-xs" style="color: #6B7280;">Acuerdo entre partes</p>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-4 rounded-lg cursor-pointer transition-all duration-150"
                           style="border: 2px solid #E5E7EB; background: #FAFAFA;"
                           onmouseover="this.style.borderColor='#F59E0B'"
                           onmouseout="this.style.borderColor='#E5E7EB'">
                        <input type="radio" name="resolucion_tipo" value="desistido" required class="w-4 h-4 accent-amber-500">
                        <div>
                            <p class="text-sm font-medium" style="color: #111827;">Desistido</p>
                            <p class="text-xs" style="color: #6B7280;">Desistimiento del proceso</p>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-4 rounded-lg cursor-pointer transition-all duration-150"
                           style="border: 2px solid #E5E7EB; background: #FAFAFA;"
                           onmouseover="this.style.borderColor='#7C3AED'"
                           onmouseout="this.style.borderColor='#E5E7EB'">
                        <input type="radio" name="resolucion_tipo" value="desestimado" required class="w-4 h-4 accent-violet-600">
                        <div>
                            <p class="text-sm font-medium" style="color: #111827;">Desestimado</p>
                            <p class="text-xs" style="color: #6B7280;">Desestimado por el consultorio</p>
                        </div>
                    </label>
                </div>
            </div>

            <div>
                <label class="text-sm font-medium mb-1 block" style="color: #374151;">Fecha de resolución</label>
                <input type="date" name="resolucion_fecha" value="{{ old('resolucion_fecha', now()->toDateString()) }}" required
                       class="w-full rounded-lg px-3 py-2.5 text-sm outline-none"
                       style="border: 1px solid #E5E7EB; background: #fff;">
            </div>

            <div>
                <label class="text-sm font-medium mb-1 block" style="color: #374151;">Notas de resolución <span class="text-xs" style="color: #9CA3AF;">(opcional)</span></label>
                <textarea name="resolucion_notas" rows="4" class="w-full rounded-lg px-3 py-2.5 text-sm outline-none"
                          style="border: 1px solid #E5E7EB; background: #fff;"
                          placeholder="Detalles adicionales sobre la resolución del caso...">{{ old('resolucion_notas') }}</textarea>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="px-5 py-2.5 rounded-lg text-sm font-medium transition-all"
                        style="background: #DC2626; color: white;"
                        onmouseover="this.style.background='#B91C1C'"
                        onmouseout="this.style.background='#DC2626'">
                    Cerrar caso
                </button>
                <a href="{{ route('casos.show', $caso->caso_numero_expediente) }}"
                   class="px-5 py-2.5 rounded-lg text-sm font-medium text-center transition-all"
                   style="background: #F3F4F6; color: #374151; border: 1px solid #E5E7EB;"
                   onmouseover="this.style.background='#E5E7EB'"
                   onmouseout="this.style.background='#F3F4F6'">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
