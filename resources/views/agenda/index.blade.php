@extends('layouts.app')

@section('title', 'Agenda')

@section('content')
<div x-data="{ mes: {{ now()->month }}, año: {{ now()->year }} }">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-5">
        <h1 class="text-xl font-bold" style="color: #111827;">Agenda de audiencias</h1>
    </div>

    <div class="grid grid-cols-3 gap-6">
        {{-- Calendario --}}
        <div class="col-span-2 rounded-xl p-5" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
            <div class="flex items-center justify-between mb-4">
                <button @click="mes = mes === 1 ? 12 : mes - 1; if(mes === 12) año--"
                        class="p-1.5 rounded-lg transition-colors" style="border: 1px solid #E5E7EB;"
                        onmouseover="this.style.background='#F3F4F6';" onmouseout="this.style.background='transparent';">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6B7280" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                </button>
                <h3 class="text-sm font-semibold" style="color: #111827;" x-text="`${['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'][mes - 1]} ${año}`"></h3>
                <button @click="mes = mes === 12 ? 1 : mes + 1; if(mes === 1) año++"
                        class="p-1.5 rounded-lg transition-colors" style="border: 1px solid #E5E7EB;"
                        onmouseover="this.style.background='#F3F4F6';" onmouseout="this.style.background='transparent';">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6B7280" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                </button>
            </div>

            {{-- Días de la semana --}}
            <div class="grid grid-cols-7 gap-1 mb-2">
                @foreach (['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'] as $dia)
                <div class="text-center text-xs font-medium py-1" style="color: #9CA3AF;">{{ $dia }}</div>
                @endforeach
            </div>

            {{-- Días del calendario --}}
            <div class="grid grid-cols-7 gap-1">
                @php
                    $primerDia = now()->startOfMonth();
                    $diaSemanaInicio = ($primerDia->dayOfWeekIso - 1); // 0 = lunes
                    $diasEnMes = now()->daysInMonth;
                @endphp
                @for ($i = 0; $i < $diaSemanaInicio; $i++)
                <div></div>
                @endfor
                @for ($dia = 1; $dia <= $diasEnMes; $dia++)
                @php
                    $fechaActual = now()->setDay($dia)->toDateString();
                    $tieneAudiencia = $audiencias->first(fn($a) => $a->audiencia_fecha == $fechaActual);
                    $esHoy = $fechaActual === now()->toDateString();
                @endphp
                <div class="text-center py-2 rounded-lg text-sm relative {{ $esHoy ? 'font-bold' : '' }}"
                     style="{{ $esHoy ? 'background: #2563EB; color: white;' : ($tieneAudiencia ? 'background: #EFF6FF; color: #2563EB;' : 'color: #374151;') }}">
                    {{ $dia }}
                    @if ($tieneAudiencia)
                    <span class="absolute bottom-1 left-1/2 -translate-x-1/2 rounded-full" style="width: 4px; height: 4px; background: #2563EB; display: inline-block;"></span>
                    @endif
                </div>
                @endfor
            </div>
        </div>

        {{-- Próximas audiencias --}}
        <div class="rounded-xl" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
            <div class="px-4 py-3" style="border-bottom: 1px solid #E5E7EB;">
                <h3 class="text-sm font-semibold" style="color: #111827;">Próximas audiencias</h3>
            </div>
            <div class="divide-y" style="border-color: #E5E7EB;">
                @forelse ($proximas as $aud)
                <div class="px-4 py-3 transition-colors" style="cursor: pointer;" onclick="window.location='{{ route('casos.show', $aud->caso->caso_numero_expediente) }}'" onmouseover="this.style.background='#F9FAFB';" onmouseout="this.style.background='transparent';">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-xs font-semibold" style="color: #2563EB;">{{ \Carbon\Carbon::parse($aud->audiencia_fecha)->format('d/m/Y') }}</span>
                        <span class="text-xs" style="color: #9CA3AF;">{{ \Carbon\Carbon::parse($aud->audiencia_hora)->format('H:i') }}</span>
                    </div>
                    <p class="text-sm font-medium" style="color: #111827;">{{ $aud->audiencia_tipo }}</p>
                    <p class="text-xs" style="color: #6B7280;">
                        {{ $aud->caso->caso_numero_expediente }} — {{ $aud->procurador?->nombre_completo ?? 'N/A' }}
                    </p>
                </div>
                @empty
                <div class="px-4 py-8 text-center text-sm" style="color: #9CA3AF;">No hay audiencias programadas</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Todas las audiencias --}}
    <div class="rounded-xl p-5 mt-6" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
        <h3 class="text-sm font-semibold mb-4" style="color: #111827;">Todas las audiencias</h3>
        <table class="w-full text-sm">
            <thead>
                <tr style="border-bottom: 1px solid #E5E7EB;">
                    <th class="text-left py-2 font-medium" style="color: #6B7280;">Fecha</th>
                    <th class="text-left py-2 font-medium" style="color: #6B7280;">Hora</th>
                    <th class="text-left py-2 font-medium" style="color: #6B7280;">Expediente</th>
                    <th class="text-left py-2 font-medium" style="color: #6B7280;">Tipo</th>
                    <th class="text-left py-2 font-medium" style="color: #6B7280;">Juzgado</th>
                    <th class="text-left py-2 font-medium" style="color: #6B7280;">Procurador</th>
                    <th class="text-left py-2 font-medium" style="color: #6B7280;">Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($audiencias as $aud)
                <tr class="transition-colors" style="cursor: pointer; border-bottom: 1px solid #F3F4F6;" onclick="window.location='{{ route('casos.show', $aud->caso->caso_numero_expediente) }}'" onmouseover="this.style.background='#F9FAFB';" onmouseout="this.style.background='transparent';">
                    <td class="py-2.5" style="color: #111827;">{{ \Carbon\Carbon::parse($aud->audiencia_fecha)->format('d/m/Y') }}</td>
                    <td class="py-2.5" style="color: #6B7280;">{{ \Carbon\Carbon::parse($aud->audiencia_hora)->format('H:i') }}</td>
                    <td class="py-2.5 font-medium" style="color: #2563EB;">{{ $aud->caso->caso_numero_expediente }}</td>
                    <td class="py-2.5" style="color: #374151;">{{ $aud->audiencia_tipo }}</td>
                    <td class="py-2.5"><span class="px-1.5 py-0.5 rounded text-xs" style="background: #F3F4F6; color: #6B7280;">{{ $aud->audiencia_juzgado }}</span></td>
                    <td class="py-2.5" style="color: #6B7280;">{{ $aud->procurador?->nombre_completo ?? 'N/A' }}</td>
                    <td class="py-2.5">
                        @if ($aud->audiencia_estado === 'pendiente')
                        <span class="text-xs px-2 py-0.5 rounded font-medium" style="background: #FEF3C7; color: #92400E;">Pendiente</span>
                        @elseif ($aud->audiencia_estado === 'realizada')
                        <span class="text-xs px-2 py-0.5 rounded font-medium" style="background: #D1FAE5; color: #065F46;">Realizada</span>
                        @else
                        <span class="text-xs px-2 py-0.5 rounded font-medium" style="background: #F3F4F6; color: #6B7280;">{{ $aud->audiencia_estado }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-8 text-center" style="color: #9CA3AF;">No hay audiencias registradas</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
