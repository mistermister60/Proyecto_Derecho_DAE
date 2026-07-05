@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div x-data="dashboardData()" x-init="initCharts()">
    {{-- KPIs --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3 md:gap-4 mb-6">
        <x-kpi-card titulo="Casos activos" :valor="$casosActivos" color="#2563EB"
            icono='<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>' />

        <x-kpi-card titulo="Nuevos del mes" :valor="$nuevosEsteMes" color="#16A34A"
            icono='<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>'
            subtexto="vs mes anterior" />

        <x-kpi-card titulo="Audiencias esta semana" :valor="$audienciasEstaSemana" color="#F59E0B"
            icono='<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>' />

        <x-kpi-card titulo="Cerrados" :valor="$cerrados" color="#6B7280"
            icono='<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>' />

        <x-kpi-card titulo="Casos atrasados" :valor="$atrasados" color="#EF4444"
            icono='<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>' />
    </div>

    {{-- Gráficas --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6 mb-6">
        <div class="lg:col-span-2 rounded-xl p-4 md:p-5" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
            <h3 class="text-sm font-semibold mb-4" style="color: #111827;">Casos por estado del pipeline</h3>
            <div class="overflow-x-auto">
                <canvas id="pipelineChart" height="200"></canvas>
            </div>
        </div>

        <div class="rounded-xl p-4 md:p-5" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
            <h3 class="text-sm font-semibold mb-4" style="color: #111827;">Por tipo de trámite</h3>
            <div class="overflow-x-auto">
                <canvas id="tipoChart" height="200"></canvas>
            </div>
        </div>
    </div>

    {{-- Audiencias + Carga procuradores --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6">
        {{-- Próximas audiencias --}}
        <div class="rounded-xl" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
            <div class="flex items-center justify-between px-4 py-3 md:px-5 md:py-4" style="border-bottom: 1px solid #E5E7EB;">
                <h3 class="text-sm font-semibold" style="color: #111827;">Próximas audiencias</h3>
                <a href="{{ route('agenda.index') }}" class="text-xs font-medium flex items-center gap-1 transition-colors" style="color: #2563EB;">
                    Ver todo
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </a>
            </div>
            <div class="divide-y" style="border-color: #E5E7EB;">
                @forelse ($proximasAudiencias as $aud)
                <div class="flex items-center gap-3 md:gap-4 px-4 py-2.5 md:px-5 md:py-3 transition-colors" style="cursor: pointer;" onclick="window.location='{{ route('casos.show', $aud->caso->caso_numero_expediente) }}'" onmouseover="this.style.background='#F9FAFB';" onmouseout="this.style.background='transparent';">
                    <div class="text-center shrink-0">
                        <div class="text-sm font-bold" style="color: #2563EB;">{{ \Carbon\Carbon::parse($aud->audiencia_hora)->format('H:i') }}</div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium" style="color: #111827;">{{ $aud->caso->caso_numero_expediente }}</span>
                            <span class="text-xs px-1.5 py-0.5 rounded hidden sm:inline" style="background: #F3F4F6; color: #6B7280;">{{ $aud->audiencia_juzgado }}</span>
                        </div>
                        <p class="text-xs mt-0.5 truncate" style="color: #6B7280;">{{ $aud->audiencia_tipo }} — {{ $aud->procurador->nombre_completo }}</p>
                    </div>
                    <span class="text-xs font-medium shrink-0" style="color: #9CA3AF;">{{ \Carbon\Carbon::parse($aud->audiencia_fecha)->format('d/m') }}</span>
                </div>
                @empty
                <div class="px-4 py-8 text-center text-sm" style="color: #9CA3AF;">No hay audiencias programadas</div>
                @endforelse
            </div>
        </div>

        {{-- Carga por procurador --}}
        <div class="rounded-xl" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
            <div class="px-4 py-3 md:px-5 md:py-4" style="border-bottom: 1px solid #E5E7EB;">
                <h3 class="text-sm font-semibold" style="color: #111827;">Carga por procurador</h3>
            </div>
            <div class="divide-y" style="border-color: #E5E7EB;">
                @foreach ($procuradores as $proc)
                <div class="flex items-center gap-3 px-4 py-2.5 md:px-5 md:py-3">
                    <div class="flex items-center justify-center rounded-full shrink-0" style="width: 32px; height: 32px; background: #1E3A5F; color: white; font-size: 11px; font-weight: 600;">
                        {{ strtoupper(substr($proc->procurador_nombre, 0, 1)) }}{{ strtoupper(substr($proc->procurador_apellido, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium truncate" style="color: #111827;">{{ $proc->nombre_completo }}</p>
                        <p class="text-xs" style="color: #9CA3AF;">Carnet {{ $proc->procurador_carnet }}</p>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="text-sm font-bold" style="color: #111827;">{{ $proc->total_casos }}</p>
                        <p class="text-xs" style="color: #6B7280;">{{ $proc->activos }} activos</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function dashboardData() {
    return {
        initCharts() {
            // Pipeline chart
            new Chart(document.getElementById('pipelineChart'), {
                type: 'bar',
                data: {
                    labels: @json($pipelineLabels),
                    datasets: [{
                        label: 'Casos',
                        data: @json($pipelineData),
                        backgroundColor: @json($pipelineColors),
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
                    labels: @json($tipoLabels),
                    datasets: [{
                        data: @json($tipoData),
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
