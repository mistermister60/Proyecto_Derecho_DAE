@extends('layouts.app')

@section('title', "Caso {$caso->caso_numero_expediente}")

@section('content')
<div x-data="{ tab: 'resumen' }">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-5">
        <div>
            <h1 class="text-xl font-bold" style="color: #111827;">Expediente {{ $caso->caso_numero_expediente }}</h1>
            <p class="text-sm mt-1" style="color: #6B7280;">
                {{ $caso->cliente?->nombre_completo ?? 'Sin cliente' }}
                <span class="mx-2">•</span>
                {{ $caso->tipoTramite?->tramite_nombre ?? 'N/A' }}
            </p>
        </div>
        <div class="flex items-center gap-2">
            <x-estado-badge :estado="$caso->estado?->estado_nombre ?? 'N/A'" />
@if (strtolower(Auth::user()->rol?->rol_nombre ?? '') !== 'procurador')
                <a href="{{ route('casos.reasignar', $caso->caso_numero_expediente) }}" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all"
                   style="background: #A855F7; color: white;" onmouseover="this.style.background='#9333EA';" onmouseout="this.style.background='#A855F7';">
                    Reasignar
                </a>
            @endif
            @if (strtolower(Auth::user()->rol?->rol_nombre ?? '') !== 'procurador')
            <a href="{{ route('casos.edit', $caso->caso_numero_expediente) }}" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all"
               style="background: #F3F4F6; color: #374151; border: 1px solid #E5E7EB;" onmouseover="this.style.background='#E5E7EB';" onmouseout="this.style.background='#F3F4F6';">
                Editar
            </a>
            @endif
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex gap-1 mb-5 rounded-lg" style="background: #F3F4F6; padding: 3px;">
        <button @click="tab = 'resumen'" class="px-4 py-2 text-sm font-medium rounded-md transition-all"
                :style="tab === 'resumen' ? 'background: white; color: #111827; box-shadow: 0 1px 2px rgba(0,0,0,0.05);' : 'color: #6B7280;'">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="inline mr-1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            Resumen
        </button>
        <button @click="tab = 'hechos'" class="px-4 py-2 text-sm font-medium rounded-md transition-all"
                :style="tab === 'hechos' ? 'background: white; color: #111827; box-shadow: 0 1px 2px rgba(0,0,0,0.05);' : 'color: #6B7280;'">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="inline mr-1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            Relación de hechos
        </button>
        <button @click="tab = 'documentos'" class="px-4 py-2 text-sm font-medium rounded-md transition-all"
                :style="tab === 'documentos' ? 'background: white; color: #111827; box-shadow: 0 1px 2px rgba(0,0,0,0.05);' : 'color: #6B7280;'">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="inline mr-1.5"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
            Documentos
        </button>
        <button @click="tab = 'bitacora'" class="px-4 py-2 text-sm font-medium rounded-md transition-all"
                :style="tab === 'bitacora' ? 'background: white; color: #111827; box-shadow: 0 1px 2px rgba(0,0,0,0.05);' : 'color: #6B7280;'">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="inline mr-1.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            Bitácora
        </button>
    </div>

    {{-- TAB: RESUMEN --}}
    <div x-show="tab === 'resumen'" class="grid grid-cols-2 gap-6">
        <div class="rounded-xl p-5" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
            <h3 class="text-sm font-semibold mb-4" style="color: #111827;">Información del caso</h3>
            <dl class="space-y-3">
                <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">Expediente</dt><dd class="text-sm font-medium" style="color: #111827;">{{ $caso->caso_numero_expediente }}</dd></div>
                <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">Estado</dt><dd><x-estado-badge :estado="$caso->estado?->estado_nombre ?? 'N/A'" /></dd></div>
                <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">Tipo de trámite</dt><dd class="text-sm font-medium" style="color: #111827;">{{ $caso->tipoTramite?->tramite_nombre ?? 'N/A' }}</dd></div>
                <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">Parte representada</dt><dd class="text-sm font-medium" style="color: #111827;">{{ $caso->caso_parte_representada }}</dd></div>
                <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">Juzgado</dt><dd class="text-sm font-medium" style="color: #111827;">{{ $caso->caso_juzgado ?? 'Pendiente' }}</dd></div>
                <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">Fecha de interposición</dt><dd class="text-sm font-medium" style="color: #111827;">{{ \Carbon\Carbon::parse($caso->caso_fecha_interpuesta)->format('d/m/Y') }}</dd></div>
                <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">Fecha de asignación</dt><dd class="text-sm font-medium" style="color: #111827;">{{ \Carbon\Carbon::parse($caso->caso_fecha_asignacion)->format('d/m/Y') }}</dd></div>
                <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">Admisible</dt><dd class="text-sm font-medium" style="color: #111827;">{{ $caso->caso_admisible ? 'Sí' : 'No' }}</dd></div>

                <div class="flex justify-between"><dt class="text-sm" style="color: #6B7280;">Cantidad de hijos</dt><dd class="text-sm font-medium" style="color: #111827;">{{ $caso->cliente?->cliente_numero_hijos ?? 0 }}</dd></div>
                <div class="flex justify-between flex-col sm:flex-row gap-1"><dt class="text-sm" style="color: #6B7280;">Nombres de los hijos</dt><dd class="text-sm font-medium text-right" style="color: #111827;">{{ $caso->cliente?->cliente_nombres_hijos ?? 'Ninguno' }}</dd></div>
            </dl>
        </div>

        <div class="rounded-xl p-5" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
            <h3 class="text-sm font-semibold mb-4" style="color: #111827;">Partes del proceso</h3>
            <dl class="space-y-3">
                <div>
                    <dt class="text-sm font-medium mb-1" style="color: #111827;">Cliente / Demandante</dt>
                    <div class="p-3 rounded-lg" style="background: #F9FAFB;">
                        <p class="text-sm font-medium" style="color: #111827;">{{ $caso->cliente?->nombre_completo ?? 'N/A' }}</p>
                        <p class="text-xs mt-1" style="color: #6B7280;">DNI: {{ $caso->cliente?->cliente_dni ?? 'N/A' }} • Tel: {{ $caso->cliente?->cliente_telefono ?? 'N/A' }}</p>
                    </div>
                </div>
                @if ($caso->demandado)
                <div>
                    <dt class="text-sm font-medium mb-1" style="color: #111827;">Demandado</dt>
                    <div class="p-3 rounded-lg" style="background: #F9FAFB;">
                        <p class="text-sm font-medium" style="color: #111827;">{{ $caso->demandado->nombre_completo }}</p>
                        <p class="text-xs mt-1" style="color: #6B7280;">DNI: {{ $caso->demandado->demandado_dni }} • Tel: {{ $caso->demandado->demandado_telefono }}</p>
                    </div>
                </div>
                @endif
                <div>
                    <dt class="text-sm font-medium mb-1" style="color: #111827;">Procurador asignado</dt>
                    <div class="p-3 rounded-lg" style="background: #F9FAFB;">
                        <p class="text-sm font-medium" style="color: #111827;">{{ $caso->procurador?->nombre_completo ?? 'No asignado' }}</p>
                        <p class="text-xs mt-1" style="color: #6B7280;">Carnet: {{ $caso->procurador?->procurador_carnet ?? 'N/A' }}</p>
                    </div>
                </div>
            </dl>
        </div>

        @if ($caso->caso_observaciones_director)
        <div class="col-span-2 rounded-xl p-5" style="background: #FEF9E7; border: 1px solid #FDE68A;">
            <h3 class="text-sm font-semibold mb-2" style="color: #92400E;">Observaciones del Director</h3>
            <p class="text-sm" style="color: #78350F;">{{ $caso->caso_observaciones_director }}</p>
        </div>
        @endif
    </div>

    {{-- TAB: RELACIÓN DE HECHOS --}}
    <div x-show="tab === 'hechos'" class="rounded-xl p-5" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
        <h3 class="text-sm font-semibold mb-4" style="color: #111827;">Relación de hechos</h3>
        <div class="prose prose-sm max-w-none" style="color: #374151;">
            <p>{{ $caso->caso_relacion_hechos }}</p>
        </div>

        @if ($caso->entrevistas->count() > 0)
        <h3 class="text-sm font-semibold mt-6 mb-3" style="color: #111827;">Entrevistas realizadas</h3>
        <div class="space-y-3">
            @foreach ($caso->entrevistas as $entrevista)
            <div class="p-4 rounded-lg" style="background: #F9FAFB;">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium" style="color: #111827;">{{ $entrevista->procurador?->nombre_completo ?? 'N/A' }}</span>
                    <span class="text-xs" style="color: #6B7280;">{{ \Carbon\Carbon::parse($entrevista->entrevista_fecha)->format('d/m/Y') }}</span>
                </div>
                <p class="text-sm" style="color: #374151;">{{ $entrevista->entrevista_relacion_hechos }}</p>
                @if ($entrevista->entrevista_observaciones)
                <p class="text-xs mt-2" style="color: #6B7280;"><em>{{ $entrevista->entrevista_observaciones }}</em></p>
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- TAB: DOCUMENTOS --}}
    <div x-show="tab === 'documentos'" class="rounded-xl p-5" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold" style="color: #111827;">Documentos del caso</h3>
        </div>
        @forelse ($caso->documentos as $doc)
        <div class="flex items-center gap-4 p-3 rounded-lg mb-2" style="border: 1px solid #E5E7EB;">
            <div class="flex items-center justify-center rounded-lg shrink-0" style="width: 40px; height: 40px; background: #F3F4F6;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#6B7280" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium" style="color: #111827;">{{ $doc->documento_nombre }}</p>
                <p class="text-xs" style="color: #6B7280;">{{ $doc->documento_tipo }} • {{ $doc->documento_tamano ? round($doc->documento_tamano / 1024, 1) . ' KB' : '—' }}</p>
            </div>
        </div>
        @empty
        <div class="py-8 text-center">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#D1D5DB" stroke-width="1.5" class="mx-auto mb-3"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
            <p class="text-sm" style="color: #9CA3AF;">No hay documentos adjuntos</p>
        </div>
        @endforelse
    </div>

    {{-- TAB: BITÁCORA --}}
    <div x-show="tab === 'bitacora'" class="rounded-xl p-5" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
        
        {{-- Formulario para registrar nuevo seguimiento --}}
        <div class="mb-8 p-5 rounded-xl bg-gray-50 border border-gray-200" x-data="{ 
            descripcion: '',
            tipo: 'Trámite',
            aplicarPlantilla(texto, tipoSujeto) {
                this.descripcion = texto;
                this.tipo = tipoSujeto;
            }
        }">
            <h4 class="text-sm font-semibold mb-4 text-gray-900">Registrar nuevo seguimiento en la bitácora</h4>
            
            <form action="{{ route('seguimientos.store', $caso->caso_id) }}" method="POST" class="space-y-4">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Selector de Tipo de Seguimiento --}}
                    <div class="md:col-span-1">
                        <label for="seguimiento_tipo" class="block text-xs font-medium text-gray-700 mb-1">Tipo de seguimiento</label>
                        <select id="seguimiento_tipo" name="seguimiento_tipo" x-model="tipo" required
                                class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 bg-white border">
                            <option value="Presentación">Presentación</option>
                            <option value="Admisión">Admisión</option>
                            <option value="Notificación">Notificación</option>
                            <option value="Audiencia">Audiencia</option>
                            <option value="Trámite">Trámite</option>
                            <option value="Inicio">Inicio desde cero</option>
                        </select>
                    </div>

                    {{-- Botones de Plantillas Rápidas basadas en image_a51ad7.png --}}
                    <div class="md:col-span-2">
<span class="block text-xs font-medium text-gray-700 mb-1">Plantillas rápidas (Clic para aplicar)</span>
<div class="flex flex-wrap gap-1.5">
    {{-- Presentado al Juzgado --}}
    <button type="button" @click="aplicarPlantilla('YA FUE PRESENTADO AL JUZGADO', 'Presentación')" 
            class="text-xs bg-white border border-gray-300 text-gray-700 px-2 py-1 rounded-md hover:bg-gray-100 transition inline-flex items-center">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mr-1.5 text-gray-500"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        Presentado al Juzgado
    </button>

    {{-- Admitida (Hoy) --}}
    <button type="button" @click="aplicarPlantilla('ADMITIDA EN FECHA {{ now()->format('d/m/Y') }}', 'Admisión')" 
            class="text-xs bg-white border border-gray-300 text-gray-700 px-2 py-1 rounded-md hover:bg-gray-100 transition inline-flex items-center">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mr-1.5 text-green-600"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        Admitida (Hoy)
    </button>

    {{-- Espera Audiencia --}}
    <button type="button" @click="aplicarPlantilla('EN ESPERA DE SEÑALAMIENTO DE AUDIENCIA POR PARTE DEL JUZGADO', 'Audiencia')" 
            class="text-xs bg-white border border-gray-300 text-gray-700 px-2 py-1 rounded-md hover:bg-gray-100 transition inline-flex items-center">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mr-1.5 text-amber-500"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        Espera Audiencia
    </button>

    {{-- Emplazado --}}
    <button type="button" @click="aplicarPlantilla('YA FUE EMPLAZADO EL DEMANDANTE POR PARTE DEL JUZGADO', 'Notificación')" 
            class="text-xs bg-white border border-gray-300 text-gray-700 px-2 py-1 rounded-md hover:bg-gray-100 transition inline-flex items-center">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mr-1.5 text-blue-500"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
        Emplazado
    </button>

    {{-- Confirió / Presentó Poder --}}
    <button type="button" @click="aplicarPlantilla('SE CONFIRIO PODER POR PARTE DE LA DEMANDANTE Y SE PRESENTO PODER YA ME NOTIFIQUE AL JUZGADO EL DIA ', 'Notificación')" 
            class="text-xs bg-white border border-gray-300 text-gray-700 px-2 py-1 rounded-md hover:bg-gray-100 transition inline-flex items-center">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mr-1.5 text-purple-500"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><polyline points="17 11 19 13 23 9"/></svg>
        Confirió/Presentó Poder
    </button>

    {{-- Desde Cero --}}
    <button type="button" @click="aplicarPlantilla('CASO QUE SE LLEVARA DESDE CERO', 'Inicio')" 
            class="text-xs bg-white border border-gray-300 text-gray-700 px-2 py-1 rounded-md hover:bg-gray-100 transition inline-flex items-center">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mr-1.5 text-indigo-500"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
        Desde Cero
    </button>
</div>
                    </div>
                </div>

                {{-- Campo de Descripción --}}
                <div>
                    <label for="seguimiento_descripcion" class="block text-xs font-medium text-gray-700 mb-1">Descripción del seguimiento</label>
                    <textarea id="seguimiento_descripcion" name="seguimiento_descripcion" rows="3" required x-model="descripcion"
                              placeholder="Escribe los detalles aquí o selecciona una plantilla arriba..."
                              class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 bg-white border"></textarea>
                </div>

                {{-- Botón de Envío --}}
                <div class="flex justify-end">
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition shadow-sm">
                        Guardar en Bitácora
                    </button>
                </div>
            </form>
        </div>

        <hr class="border-gray-200 mb-6" />

        {{-- Historial de Seguimientos (Código existente modificado para mantener estructura) --}}
        <h3 class="text-sm font-semibold mb-4" style="color: #111827;">Bitácora de seguimiento</h3>

        @forelse ($caso->seguimientos as $seg)
        <div class="flex gap-4 pb-4 mb-4" style="border-bottom: 1px solid #F3F4F6;">
            <div class="flex flex-col items-center">
                <div class="rounded-full shrink-0" style="width: 10px; height: 10px; background: #2563EB;"></div>
                <div class="flex-1 w-px mt-1" style="background: #E5E7EB;"></div>
            </div>
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-sm font-medium" style="color: #111827;">{{ $seg->usuario?->usuario_nombre ?? 'Sistema' }}</span>
                    <span class="text-xs px-1.5 py-0.5 rounded" style="background: #F3F4F6; color: #6B7280;">{{ $seg->seguimiento_tipo }}</span>
                    <span class="text-xs" style="color: #9CA3AF; margin-left: auto;">{{ \Carbon\Carbon::parse($seg->seguimiento_fecha)->format('d/m/Y') }}</span>
                </div>
                <p class="text-sm" style="color: #374151;">{{ $seg->seguimiento_descripcion }}</p>
            </div>
        </div>
        @empty
        <div class="py-8 text-center text-sm" style="color: #9CA3AF;">No hay seguimientos registrados</div>
        @endforelse

        {{-- Audiencias (Tu bloque de código original de audiencias se mantiene intacto aquí abajo) --}}
        @if ($caso->audiencias->count() > 0)
        <h3 class="text-sm font-semibold mt-6 mb-3" style="color: #111827;">Audiencias</h3>
        <div class="space-y-2">
            @foreach ($caso->audiencias as $aud)
            <div class="flex items-center gap-3 p-3 rounded-lg" style="background: #F9FAFB;">
                <div class="text-center shrink-0" style="min-width: 48px;">
                    <div class="text-sm font-bold" style="color: #2563EB;">{{ \Carbon\Carbon::parse($aud->audiencia_fecha)->format('d/m') }}</div>
                    <div class="text-xs" style="color: #6B7280;">{{ \Carbon\Carbon::parse($aud->audiencia_hora)->format('H:i') }}</div>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium" style="color: #111827;">{{ $aud->audiencia_tipo }}</p>
                    <p class="text-xs" style="color: #6B7280;">Juzgado {{ $aud->audiencia_juzgado }}</p>
                </div>
                @if ($aud->audiencia_estado === 'pendiente')
                <span class="text-xs px-2 py-1 rounded font-medium" style="background: #FEF3C7; color: #92400E;">Pendiente</span>
                @else
                <span class="text-xs px-2 py-1 rounded font-medium" style="background: #D1FAE5; color: #065F46;">{{ ucfirst($aud->audiencia_estado) }}</span>
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection
