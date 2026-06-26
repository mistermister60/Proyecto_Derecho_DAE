@extends('layouts.app')

@section('title', 'Detalle del expediente')

@section('content')
<div x-data="{ tab: 'resumen' }">
    {{-- Cabecera del caso --}}
    <div class="rounded-xl p-5 mb-6" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
        <div class="flex items-start justify-between">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="text-lg font-bold" style="color: #111827;">0501-2026-00431</span>
                    <x-estado-badge estado="Audiencia señalada" />
                    <span class="text-xs px-2 py-0.5 rounded" style="background: #FEF3C7; color: #B45309;">Urgente</span>
                </div>
                <h2 class="text-sm" style="color: #6B7280;">Ena Elizabeth Flores Álvarez <span style="color: #9CA3AF;">vs</span> Héctor Manuel Flores Cruz</h2>
                <p class="text-xs mt-1" style="color: #9CA3AF;">Divorcio contencioso — Juzgado J-7</p>
            </div>
            <div class="flex items-center gap-2">
                <button class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all"
                        style="border: 1px solid #E5E7EB; color: #6B7280; background: #FFFFFF;"
                        onmouseover="this.style.background='#F9FAFB';" onmouseout="this.style.background='#FFFFFF';">
                    Editar
                </button>
                <a href="{{ route('casos.reasignar', '0501-2026-00431') }}"
                   class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all"
                   style="border: 1px solid #F59E0B; color: #B45309; background: #FFFBEB;"
                   onmouseover="this.style.background='#FEF3C7';" onmouseout="this.style.background='#FFFBEB';">
                    Reasignar
                </a>
                <button class="px-4 py-1.5 rounded-lg text-sm font-medium transition-all"
                        style="background: #2563EB; color: white;"
                        onmouseover="this.style.background='#1d4ed8';" onmouseout="this.style.background='#2563EB';">
                    Cambiar estado
                </button>
            </div>
        </div>
    </div>

    {{-- Tabs + Contenido --}}
    <div class="grid grid-cols-3 gap-6">
        {{-- Columna principal (2/3) --}}
        <div class="col-span-2">
            {{-- Tabs --}}
            <div class="flex gap-1 mb-4 rounded-lg p-1" style="background: #F3F4F6; display: inline-flex;">
                @php $tabs = ['resumen' => 'Resumen', 'hechos' => 'Relación de hechos', 'documentos' => 'Documentos', 'bitacora' => 'Bitácora']; @endphp
                @foreach ($tabs as $key => $label)
                <button @click="tab = '{{ $key }}'"
                        class="px-4 py-1.5 rounded-md text-sm font-medium transition-all"
                        :style="tab === '{{ $key }}' ? 'background: #FFFFFF; color: #111827; box-shadow: 0 1px 2px rgba(0,0,0,0.05);' : 'color: #6B7280;'">
                    {{ $label }}
                </button>
                @endforeach
            </div>

            {{-- Tab Resumen --}}
            <div x-show="tab === 'resumen'" class="rounded-xl p-5" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-xs font-semibold uppercase tracking-wider mb-3" style="color: #6B7280;">Datos del caso</h4>
                        <dl class="space-y-2">
                            <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">No. Expediente</dt><dd class="text-sm font-medium" style="color: #111827;">0501-2026-00431</dd></div>
                            <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">Tipo de trámite</dt><dd class="text-sm font-medium" style="color: #111827;">Divorcio contencioso</dd></div>
                            <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">Parte representada</dt><dd class="text-sm font-medium" style="color: #111827;">Demandante</dd></div>
                            <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">Juzgado</dt><dd class="text-sm font-medium" style="color: #111827;">J-7</dd></div>
                            <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">Fecha interpuesta</dt><dd class="text-sm font-medium" style="color: #111827;">12/05/2026</dd></div>
                            <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">Admisibilidad</dt><dd class="text-sm font-medium" style="color: #16A34A;">Admisible</dd></div>
                        </dl>
                    </div>
                    <div>
                        <h4 class="text-xs font-semibold uppercase tracking-wider mb-3" style="color: #6B7280;">Asignación</h4>
                        <dl class="space-y-2">
                            <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">Procurador entrevistador</dt><dd class="text-sm font-medium" style="color: #111827;">Iris Lizeth Rodríguez</dd></div>
                            <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">Procurador asignado</dt><dd class="text-sm font-medium" style="color: #111827;">Iris Lizeth Rodríguez</dd></div>
                            <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">Fecha de asignación</dt><dd class="text-sm font-medium" style="color: #111827;">15/05/2026</dd></div>
                            <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">Carnet</dt><dd class="text-sm font-medium" style="color: #111827;">0257-26</dd></div>
                        </dl>
                    </div>
                </div>

                <hr style="border-color: #E5E7EB; margin: 20px 0;">

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-xs font-semibold uppercase tracking-wider mb-3" style="color: #6B7280;">Cliente</h4>
                        <p class="text-sm font-medium" style="color: #111827;">Ena Elizabeth Flores Álvarez</p>
                        <p class="text-xs mt-1" style="color: #6B7280;">Identidad: 0501-1990-04521</p>
                        <p class="text-xs" style="color: #6B7280;">Estado civil: Casada</p>
                        <p class="text-xs" style="color: #6B7280;">Teléfono: 9876-5432</p>
                        <p class="text-xs" style="color: #6B7280;">Dirección: Col. Miramontes, San Pedro Sula</p>
                        <p class="text-xs" style="color: #6B7280;">Profesión: Docente</p>
                    </div>
                    <div>
                        <h4 class="text-xs font-semibold uppercase tracking-wider mb-3" style="color: #6B7280;">Demandado / Contraparte</h4>
                        <p class="text-sm font-medium" style="color: #111827;">Héctor Manuel Flores Cruz</p>
                        <p class="text-xs mt-1" style="color: #6B7280;">Identidad: 0501-1988-03147</p>
                        <p class="text-xs" style="color: #6B7280;">Estado civil: Casado</p>
                        <p class="text-xs" style="color: #6B7280;">Teléfono: 9543-2108</p>
                        <p class="text-xs" style="color: #6B7280;">Dirección: Barrio Río de Piedras, SPS</p>
                        <p class="text-xs" style="color: #6B7280;">Profesión: Contador</p>
                    </div>
                </div>
            </div>

            {{-- Tab Relación de hechos --}}
            <div x-show="tab === 'hechos'" class="rounded-xl p-5" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
                <p class="text-sm leading-relaxed" style="color: #374151;">
                    La señora Ena Elizabeth Flores Álvarez comparece ante este Consultorio Jurídico solicitando asistencia legal para iniciar proceso de divorcio contencioso en contra del señor Héctor Manuel Flores Cruz, con quien contrajo matrimonio civil el día 15 de febrero de 2015 bajo el régimen de sociedad conyugal. La solicitante manifiesta que durante la vigencia del matrimonio se han presentado diferencias irreconciliables que hacen imposible la continuación de la vida en común, incluyendo maltrato psicológico e incumplimiento de obligaciones económicas. De dicho matrimonio procrearon dos hijos menores de edad: Ana Sofía Flores Álvarez (8 años) y Diego Alejandro Flores Álvarez (5 años). La solicitante labora como docente devengando un salario mensual de L. 18,000.00.
                </p>
            </div>

            {{-- Tab Documentos --}}
            <div x-show="tab === 'documentos'" class="rounded-xl p-5" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
                <div class="divide-y" style="border-color: #E5E7EB;">
                    @php
                    $documentos = [
                        ['nombre' => 'Solicitud de divorcio.pdf', 'tipo' => 'PDF', 'fecha' => '12/05/2026', 'tamano' => '245 KB'],
                        ['nombre' => 'Certificado de matrimonio.pdf', 'tipo' => 'PDF', 'fecha' => '12/05/2026', 'tamano' => '180 KB'],
                        ['nombre' => 'Constancia de trabajo.pdf', 'tipo' => 'PDF', 'fecha' => '13/05/2026', 'tamano' => '92 KB'],
                        ['nombre' => 'Fotografías (evidencia).jpg', 'tipo' => 'IMG', 'fecha' => '14/05/2026', 'tamano' => '1.2 MB'],
                        ['nombre' => 'Estado de cuenta bancario.pdf', 'tipo' => 'PDF', 'fecha' => '15/05/2026', 'tamano' => '310 KB'],
                    ];
                    @endphp
                    @foreach ($documentos as $doc)
                    <div class="flex items-center gap-3 py-3 transition-colors" onmouseover="this.style.background='#F9FAFB';" onmouseout="this.style.background='transparent';">
                        <div class="flex items-center justify-center rounded-lg shrink-0" style="width: 36px; height: 36px; {{ $doc['tipo'] === 'PDF' ? 'background: #FEF2F2; color: #DC2626;' : 'background: #EFF6FF; color: #2563EB;' }}">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium" style="color: #111827;">{{ $doc['nombre'] }}</p>
                            <p class="text-xs" style="color: #9CA3AF;">{{ $doc['fecha'] }} — {{ $doc['tamano'] }}</p>
                        </div>
                        <a href="#" class="text-xs font-medium transition-colors" style="color: #2563EB;" onmouseover="this.style.color='#1d4ed8';" onmouseout="this.style.color='#2563EB';">Descargar</a>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Tab Bitácora --}}
            <div x-show="tab === 'bitacora'" class="rounded-xl p-5" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
                <div class="space-y-0">
                    @php
                    $bitacora = [
                        ['fecha' => '15/06/2026', 'hora' => '10:30', 'usuario' => 'Director', 'accion' => 'Se señaló audiencia para el 25/06/2026 a las 09:00 en J-7', 'tipo' => 'audiencia'],
                        ['fecha' => '10/06/2026', 'hora' => '14:15', 'usuario' => 'Iris Lizeth Rodríguez', 'accion' => 'Se presentó escrito de demanda ante el juzgado J-7', 'tipo' => 'documento'],
                        ['fecha' => '05/06/2026', 'hora' => '09:00', 'usuario' => 'Director', 'accion' => 'Caso admitido. Se asigna a la procuradora Iris Lizeth Rodríguez', 'tipo' => 'estado'],
                        ['fecha' => '02/06/2026', 'hora' => '11:20', 'usuario' => 'Iris Lizeth Rodríguez', 'accion' => 'Entrevista realizada con la cliente. Se procede a preparar documentación', 'tipo' => 'entrevista'],
                        ['fecha' => '28/05/2026', 'hora' => '08:45', 'usuario' => 'Director', 'accion' => 'Caso registrado y en espera de admisibilidad', 'tipo' => 'sistema'],
                    ];
                    @endphp
                    @foreach ($bitacora as $i => $b)
                    <div class="flex gap-4 pb-4 {{ !$loop->last ? 'relative' : '' }}">
                        @if (!$loop->last)
                        <div class="absolute left-[7px] top-4 bottom-0" style="border-left: 2px dashed #E5E7EB;"></div>
                        @endif
                        <div class="flex flex-col items-center shrink-0">
                            <div class="rounded-full" style="width: 16px; height: 16px; background: {{ $b['tipo'] === 'audiencia' ? '#F59E0B' : ($b['tipo'] === 'estado' ? '#2563EB' : ($b['tipo'] === 'documento' ? '#16A34A' : '#9CA3AF')) }};"></div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium" style="color: #111827;">{{ $b['usuario'] }}</span>
                                <span class="text-xs" style="color: #9CA3AF;">{{ $b['fecha'] }} {{ $b['hora'] }}</span>
                            </div>
                            <p class="text-sm mt-0.5" style="color: #6B7280;">{{ $b['accion'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Columna lateral (1/3) --}}
        <div class="space-y-4">
            {{-- Próxima audiencia --}}
            <div class="rounded-xl p-4" style="background: #FFFBEB; border: 1px solid #FDE68A;">
                <h4 class="text-xs font-semibold uppercase tracking-wider mb-2" style="color: #B45309;">Próxima audiencia</h4>
                <p class="text-sm font-medium" style="color: #92400E;">25 de junio de 2026 — 09:00</p>
                <p class="text-xs mt-1" style="color: #B45309;">Juzgado J-7 — Audiencia preliminar</p>
            </div>

            {{-- Historial de reasignaciones --}}
            <div class="rounded-xl p-4" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
                <h4 class="text-xs font-semibold uppercase tracking-wider mb-3" style="color: #6B7280;">Reasignaciones</h4>
                <p class="text-xs" style="color: #9CA3AF;">Sin reasignaciones previas</p>
            </div>

            {{-- Observaciones del Director --}}
            <div class="rounded-xl p-4" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
                <h4 class="text-xs font-semibold uppercase tracking-wider mb-2" style="color: #6B7280;">Observaciones del Director</h4>
                <p class="text-sm" style="color: #6B7280;">Dar seguimiento semanal al estado del caso. La próxima semana confirmar fecha de audiencia.</p>
                <p class="text-xs mt-2" style="color: #9CA3AF;">Actualizado: 15/06/2026</p>
            </div>
        </div>
    </div>
</div>
@endsection
