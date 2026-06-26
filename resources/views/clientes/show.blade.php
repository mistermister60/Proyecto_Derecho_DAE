@extends('layouts.app')

@section('title', 'Ficha del cliente')

@section('content')
<div class="grid grid-cols-3 gap-6">
    {{-- Datos del cliente (2/3) --}}
    <div class="col-span-2 space-y-6">
        {{-- Información personal --}}
        <div class="rounded-xl p-5" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
            <h3 class="text-sm font-semibold mb-4" style="color: #111827;">Información personal</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-xs font-medium" style="color: #9CA3AF;">Nombre completo</p>
                    <p class="text-sm font-medium mt-0.5" style="color: #111827;">Ena Elizabeth Flores Álvarez</p>
                </div>
                <div>
                    <p class="text-xs font-medium" style="color: #9CA3AF;">Número de identidad</p>
                    <p class="text-sm font-medium mt-0.5" style="color: #111827;">0501-1990-04521</p>
                </div>
                <div>
                    <p class="text-xs font-medium" style="color: #9CA3AF;">Estado civil</p>
                    <p class="text-sm font-medium mt-0.5" style="color: #111827;">Casada</p>
                </div>
                <div>
                    <p class="text-xs font-medium" style="color: #9CA3AF;">Teléfono</p>
                    <p class="text-sm font-medium mt-0.5" style="color: #111827;">9876-5432</p>
                </div>
                <div class="col-span-2">
                    <p class="text-xs font-medium" style="color: #9CA3AF;">Dirección</p>
                    <p class="text-sm font-medium mt-0.5" style="color: #111827;">Colonia Miramontes, bloque 3, casa #12, San Pedro Sula, Cortés</p>
                </div>
                <div>
                    <p class="text-xs font-medium" style="color: #9CA3AF;">Profesión u oficio</p>
                    <p class="text-sm font-medium mt-0.5" style="color: #111827;">Docente</p>
                </div>
                <div>
                    <p class="text-xs font-medium" style="color: #9CA3AF;">Salario mensual</p>
                    <p class="text-sm font-medium mt-0.5" style="color: #111827;">L. 18,000.00</p>
                </div>
                <div>
                    <p class="text-xs font-medium" style="color: #9CA3AF;">Lugar de trabajo</p>
                    <p class="text-sm font-medium mt-0.5" style="color: #111827;">Escuela José Trinidad Reyes</p>
                </div>
                <div>
                    <p class="text-xs font-medium" style="color: #9CA3AF;">Teléfono laboral</p>
                    <p class="text-sm font-medium mt-0.5" style="color: #111827;">2550-1234</p>
                </div>
                <div class="col-span-2">
                    <p class="text-xs font-medium" style="color: #9CA3AF;">Dirección laboral</p>
                    <p class="text-sm font-medium mt-0.5" style="color: #111827;">Barrio Los Andes, 3a calle, San Pedro Sula</p>
                </div>
                <div>
                    <p class="text-xs font-medium" style="color: #9CA3AF;">Número de hijos</p>
                    <p class="text-sm font-medium mt-0.5" style="color: #111827;">2</p>
                </div>
                <div>
                    <p class="text-xs font-medium" style="color: #9CA3AF;">Nombres de los hijos</p>
                    <p class="text-sm font-medium mt-0.5" style="color: #111827;">Ana Sofía (8), Diego Alejandro (5)</p>
                </div>
            </div>
        </div>

        {{-- Casos asociados --}}
        <div class="rounded-xl" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
            <div class="px-5 py-4" style="border-bottom: 1px solid #E5E7EB;">
                <h3 class="text-sm font-semibold" style="color: #111827;">Casos asociados</h3>
            </div>
            <x-tabla :encabezados="['No. Expediente', 'Tipo de trámite', 'Estado', 'Procurador', 'Juzgado']">
                @php
                $casosCliente = [
                    ['exp' => '0501-2026-00431', 'tipo' => 'Divorcio contencioso', 'estado' => 'Audiencia señalada', 'procurador' => 'Iris Lizeth Rodríguez', 'juzgado' => 'J-7'],
                    ['exp' => '0501-2026-00415', 'tipo' => 'Demanda de alimentos', 'estado' => 'Cerrado', 'procurador' => 'Iris Lizeth Rodríguez', 'juzgado' => 'J-3'],
                ];
                @endphp
                @foreach ($casosCliente as $caso)
                <tr class="transition-colors border-t" style="border-color: #F3F4F6; cursor: pointer;" onmouseover="this.style.background='#F9FAFB';" onmouseout="this.style.background='transparent';" onclick="window.location='{{ route('casos.show', $caso['exp']) }}'">
                    <td class="px-4 py-3 text-sm font-medium" style="color: #2563EB;">{{ $caso['exp'] }}</td>
                    <td class="px-4 py-3 text-sm" style="color: #6B7280;">{{ $caso['tipo'] }}</td>
                    <td class="px-4 py-3"><x-estado-badge :estado="$caso['estado']" /></td>
                    <td class="px-4 py-3 text-sm" style="color: #6B7280;">{{ $caso['procurador'] }}</td>
                    <td class="px-4 py-3"><span class="px-2 py-0.5 rounded text-xs font-medium" style="background: #F3F4F6; color: #6B7280;">{{ $caso['juzgado'] }}</span></td>
                </tr>
                @endforeach
            </x-tabla>
        </div>
    </div>

    {{-- Columna lateral (1/3) --}}
    <div class="space-y-4">
        <div class="rounded-xl p-5 text-center" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
            <div class="flex items-center justify-center mx-auto rounded-full" style="width: 64px; height: 64px; background: #1E3A5F; color: white; font-size: 20px; font-weight: 600;">
                EF
            </div>
            <p class="text-sm font-semibold mt-3" style="color: #111827;">Ena Elizabeth Flores Álvarez</p>
            <p class="text-xs mt-1" style="color: #9CA3AF;">Cliente desde mayo 2026</p>
            <div class="flex justify-center gap-3 mt-4">
                <a href="#" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all"
                   style="background: #2563EB; color: white;"
                   onmouseover="this.style.background='#1d4ed8';" onmouseout="this.style.background='#2563EB';">Editar</a>
                <a href="{{ route('casos.create') }}" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all"
                   style="border: 1px solid #E5E7EB; color: #6B7280;"
                   onmouseover="this.style.background='#F9FAFB';" onmouseout="this.style.background='#FFFFFF';">Nuevo caso</a>
            </div>
        </div>
    </div>
</div>
@endsection
