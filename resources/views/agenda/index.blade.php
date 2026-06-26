@extends('layouts.app')

@section('title', 'Agenda de audiencias')

@section('content')
<div x-data="{ vista: 'mes' }">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-5">
        <div class="flex items-center gap-3">
            <button class="p-2 rounded-lg transition-colors" style="border: 1px solid #E5E7EB; color: #6B7280;" onmouseover="this.style.background='#F3F4F6';" onmouseout="this.style.background='transparent';">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            </button>
            <h2 class="text-base font-semibold" style="color: #111827;">Junio 2026</h2>
            <button class="p-2 rounded-lg transition-colors" style="border: 1px solid #E5E7EB; color: #6B7280;" onmouseover="this.style.background='#F3F4F6';" onmouseout="this.style.background='transparent';">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
            <div class="flex rounded-lg overflow-hidden ml-4" style="border: 1px solid #E5E7EB;">
                <button @click="vista = 'mes'" class="px-3 py-1.5 text-sm font-medium transition-colors" :style="vista === 'mes' ? 'background: #2563EB; color: white;' : 'background: #FFFFFF; color: #6B7280;'">Mes</button>
                <button @click="vista = 'semana'" class="px-3 py-1.5 text-sm font-medium transition-colors" :style="vista === 'semana' ? 'background: #2563EB; color: white;' : 'background: #FFFFFF; color: #6B7280;'">Semana</button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-6">
        {{-- Calendario (2/3) --}}
        <div class="col-span-2 rounded-xl" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
            {{-- Días de la semana --}}
            <div class="grid grid-cols-7 gap-0" style="border-bottom: 1px solid #E5E7EB;">
                @foreach (['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'] as $dia)
                <div class="text-center py-2 text-xs font-medium" style="color: #9CA3AF;">{{ $dia }}</div>
                @endforeach
            </div>

            {{-- Grid de días --}}
            <div class="grid grid-cols-7 gap-0">
                @php
                $dias = [
                    ['num' => 1, 'eventos' => 0, 'hoy' => false],
                    ['num' => 2, 'eventos' => 0, 'hoy' => false],
                    ['num' => 3, 'eventos' => 0, 'hoy' => false],
                    ['num' => 4, 'eventos' => 0, 'hoy' => false],
                    ['num' => 5, 'eventos' => 0, 'hoy' => false],
                    ['num' => 6, 'eventos' => 0, 'hoy' => false],
                    ['num' => 7, 'eventos' => 0, 'hoy' => false],
                    ['num' => 8, 'eventos' => 0, 'hoy' => false],
                    ['num' => 9, 'eventos' => 0, 'hoy' => false],
                    ['num' => 10, 'eventos' => 0, 'hoy' => false],
                    ['num' => 11, 'eventos' => 0, 'hoy' => false],
                    ['num' => 12, 'eventos' => 0, 'hoy' => false],
                    ['num' => 13, 'eventos' => 0, 'hoy' => false],
                    ['num' => 14, 'eventos' => 0, 'hoy' => false],
                    ['num' => 15, 'eventos' => 1, 'hoy' => false],
                    ['num' => 16, 'eventos' => 1, 'hoy' => false],
                    ['num' => 17, 'eventos' => 1, 'hoy' => false],
                    ['num' => 18, 'eventos' => 1, 'hoy' => true],
                    ['num' => 19, 'eventos' => 0, 'hoy' => false],
                    ['num' => 20, 'eventos' => 0, 'hoy' => false],
                    ['num' => 21, 'eventos' => 0, 'hoy' => false],
                    ['num' => 22, 'eventos' => 0, 'hoy' => false],
                    ['num' => 23, 'eventos' => 0, 'hoy' => false],
                    ['num' => 24, 'eventos' => 0, 'hoy' => false],
                    ['num' => 25, 'eventos' => 1, 'hoy' => false],
                    ['num' => 26, 'eventos' => 1, 'hoy' => false],
                    ['num' => 27, 'eventos' => 0, 'hoy' => false],
                    ['num' => 28, 'eventos' => 0, 'hoy' => false],
                    ['num' => 29, 'eventos' => 0, 'hoy' => false],
                    ['num' => 30, 'eventos' => 0, 'hoy' => false],
                ];
                @endphp
                @foreach ($dias as $dia)
                <div class="min-h-[90px] p-1.5 transition-colors" style="border-bottom: 1px solid #F3F4F6; border-right: 1px solid #F3F4F6; {{ $dia['hoy'] ? 'background: #EFF6FF;' : '' }}" onmouseover="this.style.background='#F9FAFB';" onmouseout="this.style.background='{{ $dia['hoy'] ? '#EFF6FF' : 'transparent' }}';">
                    <span class="text-xs font-medium" style="color: {{ $dia['hoy'] ? '#2563EB' : '#6B7280' }}; {{ $dia['hoy'] ? 'display: inline-flex; align-items: center; justify-content: center; width: 24px; height: 24px; border-radius: 50%; background: #2563EB; color: white;' : '' }}">{{ $dia['num'] }}</span>
                    @if ($dia['eventos'] > 0)
                    <div class="mt-1">
                        <div class="text-xs px-1 py-0.5 rounded truncate" style="background: #DBEAFE; color: #1D4ED8;">Audiencia</div>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        {{-- Lista lateral de próximas audiencias (1/3) --}}
        <div class="rounded-xl" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
            <div class="px-4 py-4" style="border-bottom: 1px solid #E5E7EB;">
                <h3 class="text-sm font-semibold" style="color: #111827;">Próximas audiencias</h3>
            </div>
            <div class="divide-y" style="border-color: #E5E7EB;">
                @php
                $proximas = [
                    ['fecha' => '18 jun', 'hora' => '08:30', 'exp' => '0501-2026-00431', 'juzgado' => 'J-7', 'procurador' => 'Iris Lizeth Rodríguez', 'tipo' => 'Audiencia preliminar', 'destacado' => true],
                    ['fecha' => '18 jun', 'hora' => '10:00', 'exp' => '0501-2026-00428', 'juzgado' => 'J-3', 'procurador' => 'Carlos Brizuela', 'tipo' => 'Conciliación', 'destacado' => true],
                    ['fecha' => '18 jun', 'hora' => '11:30', 'exp' => '0501-2026-00415', 'juzgado' => 'J-8', 'procurador' => 'Indira Galindo', 'tipo' => 'Audiencia de pruebas', 'destacado' => true],
                    ['fecha' => '19 jun', 'hora' => '09:00', 'exp' => '0501-2026-00422', 'juzgado' => 'J-7', 'procurador' => 'Franklyn Salgado', 'tipo' => 'Sentencia', 'destacado' => false],
                    ['fecha' => '25 jun', 'hora' => '09:00', 'exp' => '0501-2026-00405', 'juzgado' => 'J-3', 'procurador' => 'Ena Flores', 'tipo' => 'Audiencia inicial', 'destacado' => false],
                ];
                @endphp
                @foreach ($proximas as $aud)
                <div class="px-4 py-3 transition-colors" style="{{ $aud['destacado'] ? 'background: #FFFBEB;' : '' }}" onmouseover="this.style.background='{{ $aud['destacado'] ? '#FEF3C7' : '#F9FAFB' }}';" onmouseout="this.style.background='{{ $aud['destacado'] ? '#FFFBEB' : 'transparent' }}';">
                    <div class="flex items-start justify-between">
                        <span class="text-xs font-semibold" style="color: {{ $aud['destacado'] ? '#B45309' : '#2563EB' }};">{{ $aud['fecha'] }} — {{ $aud['hora'] }}</span>
                        <span class="text-xs px-1.5 py-0.5 rounded" style="background: #F3F4F6; color: #6B7280;">{{ $aud['juzgado'] }}</span>
                    </div>
                    <p class="text-sm font-medium mt-1" style="color: #111827;">{{ $aud['exp'] }}</p>
                    <p class="text-xs mt-0.5" style="color: #6B7280;">{{ $aud['tipo'] }}</p>
                    <p class="text-xs mt-0.5" style="color: #9CA3AF;">{{ $aud['procurador'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
