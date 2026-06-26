@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div x-data="dashboardData()" x-init="initCharts()">
    {{-- KPIs --}}
    <div class="grid grid-cols-5 gap-4 mb-6">
        <x-kpi-card titulo="Casos activos" valor="24" color="#2563EB"
            icono='<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>' />

        <x-kpi-card titulo="Nuevos del mes" valor="8" color="#16A34A"
            icono='<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>'
            subtexto="+3 vs mes anterior" />

        <x-kpi-card titulo="Audiencias esta semana" valor="5" color="#F59E0B"
            icono='<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>' />

        <x-kpi-card titulo="Cerrados del mes" valor="3" color="#6B7280"
            icono='<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>' />

        <x-kpi-card titulo="Casos atrasados" valor="2" color="#EF4444"
            icono='<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>' />
    </div>

    {{-- Gráficas + Tablas --}}
    <div class="grid grid-cols-3 gap-6 mb-6">
        {{-- Pipeline chart --}}
        <div class="col-span-2 rounded-xl p-5" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
            <h3 class="text-sm font-semibold mb-4" style="color: #111827;">Casos por estado del pipeline</h3>
            <canvas id="pipelineChart" height="200"></canvas>
        </div>

        {{-- Tipo de trámite --}}
        <div class="rounded-xl p-5" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
            <h3 class="text-sm font-semibold mb-4" style="color: #111827;">Por tipo de trámite</h3>
            <canvas id="tipoChart" height="200"></canvas>
        </div>
    </div>

    {{-- Audiencias + Carga procuradores --}}
    <div class="grid grid-cols-2 gap-6">
        {{-- Próximas audiencias --}}
        <div class="rounded-xl" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
            <div class="flex items-center justify-between px-5 py-4" style="border-bottom: 1px solid #E5E7EB;">
                <h3 class="text-sm font-semibold" style="color: #111827;">Próximas audiencias</h3>
                <a href="{{ route('agenda.index') }}" class="text-xs font-medium flex items-center gap-1 transition-colors" style="color: #2563EB;" onmouseover="this.style.color='#1d4ed8';" onmouseout="this.style.color='#2563EB';">
                    Ver todo
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </a>
            </div>
            <div class="divide-y" style="border-color: #E5E7EB;">
                @php
                $audiencias = [
                    ['hora' => '08:30', 'exp' => '0501-2026-00431', 'juzgado' => 'J-7', 'procurador' => 'Iris Lizeth Rodríguez', 'tipo' => 'Audiencia preliminar'],
                    ['hora' => '10:00', 'exp' => '0501-2026-00428', 'juzgado' => 'J-3', 'procurador' => 'Carlos Brizuela', 'tipo' => 'Conciliación'],
                    ['hora' => '11:30', 'exp' => '0501-2026-00415', 'juzgado' => 'J-8', 'procurador' => 'Indira Galindo', 'tipo' => 'Audiencia de pruebas'],
                    ['hora' => '14:00', 'exp' => '0501-2026-00422', 'juzgado' => 'J-7', 'procurador' => 'Franklyn Salgado', 'tipo' => 'Sentencia'],
                    ['hora' => '15:30', 'exp' => '0501-2026-00405', 'juzgado' => 'J-3', 'procurador' => 'Ena Flores', 'tipo' => 'Audiencia inicial'],
                ];
                @endphp
                @foreach ($audiencias as $aud)
                <div class="flex items-center gap-4 px-5 py-3 transition-colors" onmouseover="this.style.background='#F9FAFB';" onmouseout="this.style.background='transparent';">
                    <div class="text-center shrink-0">
                        <div class="text-sm font-bold" style="color: #2563EB;">{{ $aud['hora'] }}</div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium" style="color: #111827;">{{ $aud['exp'] }}</span>
                            <span class="text-xs px-1.5 py-0.5 rounded" style="background: #F3F4F6; color: #6B7280;">{{ $aud['juzgado'] }}</span>
                        </div>
                        <p class="text-xs mt-0.5" style="color: #6B7280;">{{ $aud['tipo'] }} — {{ $aud['procurador'] }}</p>
                    </div>
                    <span class="text-xs font-medium" style="color: #9CA3AF;">Hoy</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Carga por procurador --}}
        <div class="rounded-xl" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
            <div class="px-5 py-4" style="border-bottom: 1px solid #E5E7EB;">
                <h3 class="text-sm font-semibold" style="color: #111827;">Carga por procurador</h3>
            </div>
            <div class="divide-y" style="border-color: #E5E7EB;">
                @php
                $procuradores = [
                    ['nombre' => 'Iris Lizeth Rodríguez', 'carnet' => '0257-26', 'casos' => 6, 'activos' => 4],
                    ['nombre' => 'Franklyn Geovanny Salgado', 'carnet' => '0312-26', 'casos' => 5, 'activos' => 3],
                    ['nombre' => 'Indira Pauleth Galindo', 'carnet' => '0189-26', 'casos' => 4, 'activos' => 3],
                    ['nombre' => 'Carlos Alberto Brizuela', 'carnet' => '0421-26', 'casos' => 4, 'activos' => 2],
                    ['nombre' => 'Ena Elizabeth Flores', 'carnet' => '0098-26', 'casos' => 3, 'activos' => 2],
                ];
                @endphp
                @foreach ($procuradores as $proc)
                <div class="flex items-center gap-3 px-5 py-3 transition-colors" onmouseover="this.style.background='#F9FAFB';" onmouseout="this.style.background='transparent';">
                    <div class="flex items-center justify-center rounded-full shrink-0" style="width: 32px; height: 32px; background: #1E3A5F; color: white; font-size: 11px; font-weight: 600;">
                        {{ implode('', array_map(fn($n) => $n[0], explode(' ', $proc['nombre']))) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium" style="color: #111827;">{{ $proc['nombre'] }}</p>
                        <p class="text-xs" style="color: #9CA3AF;">Carnet {{ $proc['carnet'] }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold" style="color: #111827;">{{ $proc['casos'] }}</p>
                        <p class="text-xs" style="color: #6B7280;">{{ $proc['activos'] }} activos</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- Script de gráficas --}}
@push('scripts')
<script>
function dashboardData() {
    return {
        initCharts() {
            // Pipeline chart
            new Chart(document.getElementById('pipelineChart'), {
                type: 'bar',
                data: {
                    labels: ['Entrevista', 'Admitido', 'Poder conferido', 'Presentado al juzgado', 'Admitido por el juzgado', 'Audiencia señalada', 'En sentencia', 'Cerrado'],
                    datasets: [{
                        label: 'Casos',
                        data: [3, 5, 4, 6, 2, 3, 1, 3],
                        backgroundColor: ['#9CA3AF', '#60A5FA', '#3B82F6', '#2563EB', '#1D4ED8', '#F59E0B', '#D97706', '#16A34A'],
                        borderRadius: 4,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#F3F4F6' }, ticks: { color: '#6B7280', font: { size: 11 } } },
                        x: { grid: { display: false }, ticks: { color: '#6B7280', font: { size: 10 } } }
                    }
                }
            });

            // Tipo de trámite chart
            new Chart(document.getElementById('tipoChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Divorcio contencioso', 'Disolución mutuo acuerdo', 'Alimentos', 'Revisión alimentos', 'Reconocimiento paternidad', 'Ejecución forzosa'],
                    datasets: [{
                        data: [8, 5, 4, 3, 2, 2],
                        backgroundColor: ['#1E3A5F', '#2563EB', '#3B82F6', '#60A5FA', '#93C5FD', '#BFDBFE'],
                        borderWidth: 0,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { color: '#6B7280', font: { size: 10 }, boxWidth: 10, padding: 8 }
                        }
                    },
                    cutout: '65%',
                }
            });
        }
    }
}
</script>
@endpush
@endsection
