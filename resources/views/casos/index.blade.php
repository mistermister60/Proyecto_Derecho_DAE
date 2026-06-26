@extends('layouts.app')

@section('title', 'Casos')

@section('content')
<div x-data="{ vista: 'tabla', search: '', filtroEstado: '', filtroTramite: '' }">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-5">
        <div class="flex items-center gap-3">
            <div class="flex rounded-lg overflow-hidden" style="border: 1px solid #E5E7EB;">
                <button @click="vista = 'tabla'"
                        class="px-3 py-1.5 text-sm font-medium transition-colors"
                        :style="vista === 'tabla' ? 'background: #2563EB; color: white;' : 'background: #FFFFFF; color: #6B7280;'">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="inline mr-1"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="3" y1="15" x2="21" y2="15"/></svg>
                    Tabla
                </button>
                <button @click="vista = 'kanban'"
                        class="px-3 py-1.5 text-sm font-medium transition-colors"
                        :style="vista === 'kanban' ? 'background: #2563EB; color: white;' : 'background: #FFFFFF; color: #6B7280;'">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="inline mr-1"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    Kanban
                </button>
            </div>
        </div>

        <div class="flex items-center gap-3">
            {{-- Filtros --}}
            <select x-model="filtroEstado" class="text-sm rounded-lg px-3 py-1.5 outline-none" style="border: 1px solid #E5E7EB; color: #6B7280; background: #FFFFFF;">
                <option value="">Todos los estados</option>
                <option>Entrevista</option>
                <option>Admitido</option>
                <option>Poder conferido</option>
                <option>Presentado al juzgado</option>
                <option>Admitido por el juzgado</option>
                <option>Audiencia señalada</option>
                <option>En sentencia</option>
                <option>Cerrado</option>
                <option>Inadmisible</option>
                <option>Atrasado</option>
            </select>

            <select x-model="filtroTramite" class="text-sm rounded-lg px-3 py-1.5 outline-none" style="border: 1px solid #E5E7EB; color: #6B7280; background: #FFFFFF;">
                <option value="">Todos los trámites</option>
                <option>Divorcio contencioso</option>
                <option>Disolución por mutuo acuerdo</option>
                <option>Demanda de alimentos</option>
                <option>Revisión de alimentos</option>
                <option>Reconocimiento forzoso de paternidad</option>
                <option>Solicitud de ejecución forzosa</option>
            </select>

            <a href="{{ route('casos.create') }}" class="flex items-center gap-1.5 px-4 py-1.5 rounded-lg text-sm font-medium transition-all"
               style="background: #2563EB; color: white;"
               onmouseover="this.style.background='#1d4ed8';" onmouseout="this.style.background='#2563EB';">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Nuevo caso
            </a>
        </div>
    </div>

    {{-- ==================== VISTA TABLA ==================== --}}
    <div x-show="vista === 'tabla'">
        <x-tabla :encabezados="['No. Expediente', 'Cliente', 'Tipo de trámite', 'Parte', 'Procurador', 'Juzgado', 'Estado', 'Última actualización']">
            @php
            $casos = [
                ['exp' => '0501-2026-00431', 'cliente' => 'Ena Elizabeth Flores Álvarez', 'tipo' => 'Divorcio contencioso', 'parte' => 'Demandante', 'procurador' => 'Iris Lizeth Rodríguez', 'juzgado' => 'J-7', 'estado' => 'Audiencia señalada', 'fecha' => '15/06/2026'],
                ['exp' => '0501-2026-00430', 'cliente' => 'Franklyn Geovanny Salgado Pineda', 'tipo' => 'Disolución por mutuo acuerdo', 'parte' => 'Ambas partes', 'procurador' => 'Carlos Alberto Brizuela', 'juzgado' => 'J-3', 'estado' => 'Admitido por el juzgado', 'fecha' => '14/06/2026'],
                ['exp' => '0501-2026-00429', 'cliente' => 'Indira Pauleth Galindo Vásquez', 'tipo' => 'Demanda de alimentos', 'parte' => 'Demandante', 'procurador' => 'Franklyn Geovanny Salgado', 'juzgado' => 'J-8', 'estado' => 'Presentado al juzgado', 'fecha' => '12/06/2026'],
                ['exp' => '0501-2026-00428', 'cliente' => 'Bernarda Aracely Paz Guzmán', 'tipo' => 'Revisión de alimentos', 'parte' => 'Demandada', 'procurador' => 'Iris Lizeth Rodríguez', 'juzgado' => 'J-3', 'estado' => 'Audiencia señalada', 'fecha' => '10/06/2026'],
                ['exp' => '0501-2026-00427', 'cliente' => 'Carlos Alberto Brizuela Zamora', 'tipo' => 'Reconocimiento forzoso de paternidad', 'parte' => 'Demandante', 'procurador' => 'Indira Pauleth Galindo', 'juzgado' => 'J-7', 'estado' => 'Poder conferido', 'fecha' => '08/06/2026'],
                ['exp' => '0501-2026-00426', 'cliente' => 'María José Reyes Padilla', 'tipo' => 'Divorcio contencioso', 'parte' => 'Demandante', 'procurador' => 'Carlos Alberto Brizuela', 'juzgado' => 'J-8', 'estado' => 'Cerrado', 'fecha' => '05/06/2026'],
                ['exp' => '0501-2026-00425', 'cliente' => 'Pedro Antonio Mejía López', 'tipo' => 'Solicitud de ejecución forzosa', 'parte' => 'Demandante', 'procurador' => 'Ena Elizabeth Flores', 'juzgado' => 'J-3', 'estado' => 'Entrevista', 'fecha' => '03/06/2026'],
                ['exp' => '0501-2026-00424', 'cliente' => 'Ana Cecilia García Hernández', 'tipo' => 'Disolución por mutuo acuerdo', 'parte' => 'Ambas partes', 'procurador' => 'Indira Pauleth Galindo', 'juzgado' => 'J-7', 'estado' => 'Atrasado', 'fecha' => '01/06/2026'],
            ];
            @endphp
            @foreach ($casos as $caso)
            <tr class="transition-colors border-t" style="border-color: #F3F4F6; cursor: pointer;" onmouseover="this.style.background='#F9FAFB';" onmouseout="this.style.background='transparent';" onclick="window.location='{{ route('casos.show', $caso['exp']) }}'">
                <td class="px-4 py-3 text-sm font-medium" style="color: #2563EB;">{{ $caso['exp'] }}</td>
                <td class="px-4 py-3 text-sm">{{ $caso['cliente'] }}</td>
                <td class="px-4 py-3 text-sm" style="color: #6B7280;">{{ $caso['tipo'] }}</td>
                <td class="px-4 py-3 text-sm" style="color: #6B7280;">{{ $caso['parte'] }}</td>
                <td class="px-4 py-3 text-sm" style="color: #6B7280;">{{ $caso['procurador'] }}</td>
                <td class="px-4 py-3 text-sm"><span class="px-2 py-0.5 rounded text-xs font-medium" style="background: #F3F4F6; color: #6B7280;">{{ $caso['juzgado'] }}</span></td>
                <td class="px-4 py-3"><x-estado-badge :estado="$caso['estado']" /></td>
                <td class="px-4 py-3 text-sm" style="color: #9CA3AF;">{{ $caso['fecha'] }}</td>
            </tr>
            @endforeach
        </x-tabla>
    </div>

    {{-- ==================== VISTA KANBAN ==================== --}}
    <div x-show="vista === 'kanban'" class="flex gap-4 overflow-x-auto pb-4" style="min-height: 500px;">
        @php
        $columnas = [
            'Entrevista' => ['#9CA3AF', ['0501-2026-00425' => ['Pedro Antonio Mejía López', 'Ejecución forzosa', '']]],
            'Admitido' => ['#60A5FA', []],
            'Poder conferido' => ['#3B82F6', ['0501-2026-00427' => ['Carlos Alberto Brizuela Zamora', 'Reconocimiento paternidad', '']]],
            'Presentado al juzgado' => ['#2563EB', ['0501-2026-00429' => ['Indira Pauleth Galindo Vásquez', 'Demanda alimentos', '']]],
            'Admitido por el juzgado' => ['#1D4ED8', ['0501-2026-00430' => ['Franklyn Geovanny Salgado Pineda', 'Disolución mutuo acuerdo', '']]],
            'Audiencia señalada' => ['#F59E0B', ['0501-2026-00431' => ['Ena Elizabeth Flores Álvarez', 'Divorcio contencioso', '15/06'], '0501-2026-00428' => ['Bernarda Aracely Paz Guzmán', 'Revisión alimentos', '17/06']]],
            'En sentencia' => ['#D97706', []],
            'Cerrado' => ['#16A34A', ['0501-2026-00426' => ['María José Reyes Padilla', 'Divorcio contencioso', '']]],
        ];
        @endphp

        @foreach ($columnas as $estado => [$color, $tarjetas])
        <div class="flex flex-col rounded-xl shrink-0" style="width: 220px; background: #F3F4F6;">
            <div class="flex items-center gap-2 px-3 py-3">
                <span class="rounded-full" style="width: 8px; height: 8px; background: {{ $color }}; display: inline-block;"></span>
                <span class="text-xs font-semibold uppercase tracking-wider" style="color: #6B7280;">{{ $estado }}</span>
                <span class="ml-auto text-xs font-medium px-1.5 py-0.5 rounded-full" style="background: #E5E7EB; color: #6B7280;">{{ count($tarjetas) }}</span>
            </div>
            <div class="flex-1 px-3 pb-3 space-y-2">
                @forelse ($tarjetas as $exp => [$cliente, $tipo, $fecha])
                <div class="rounded-lg p-3 cursor-pointer transition-shadow" style="background: #FFFFFF; border: 1px solid #E5E7EB; box-shadow: 0 1px 2px rgba(0,0,0,0.04);" onclick="window.location='{{ route('casos.show', $exp) }}'" onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.08)';" onmouseout="this.style.boxShadow='0 1px 2px rgba(0,0,0,0.04)';">
                    <p class="text-sm font-medium" style="color: #111827;">{{ $cliente }}</p>
                    <p class="text-xs mt-1" style="color: #6B7280;">{{ $tipo }}</p>
                    @if ($fecha)
                    <div class="flex items-center gap-1 mt-2 text-xs" style="color: #F59E0B;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        {{ $fecha }}
                    </div>
                    @endif
                </div>
                @empty
                <div class="flex items-center justify-center py-8 text-xs" style="color: #9CA3AF;">
                    Sin casos
                </div>
                @endforelse
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
