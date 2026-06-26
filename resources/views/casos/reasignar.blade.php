@extends('layouts.app')

@section('title', 'Reasignación de casos')

@section('content')
<form class="max-w-3xl mx-auto">
    {{-- Card principal --}}
    <div class="rounded-xl" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
        <div class="px-6 py-4" style="border-bottom: 1px solid #E5E7EB;">
            <h3 class="text-base font-semibold" style="color: #111827;">Reasignación de caso</h3>
            <p class="text-xs mt-1" style="color: #6B7280;">Complete la información para transferir el caso a otro procurador.</p>
        </div>

        <div class="px-6 py-5 space-y-5">
            {{-- Procurador que entrega --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium mb-1.5" style="color: #6B7280;">Procurador que entrega</label>
                    <div class="rounded-lg px-3 py-2 text-sm" style="border: 1px solid #E5E7EB; background: #F9FAFB; color: #6B7280;">
                        Iris Lizeth Rodríguez (0257-26)
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1.5" style="color: #6B7280;">Procurador que recibe</label>
                    <select class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;">
                        <option>Seleccione un procurador</option>
                        <option>Franklyn Geovanny Salgado (0312-26)</option>
                        <option>Indira Pauleth Galindo (0189-26)</option>
                        <option>Carlos Alberto Brizuela (0421-26)</option>
                        <option>Ena Elizabeth Flores (0098-26)</option>
                    </select>
                </div>
            </div>

            {{-- Motivo --}}
            <div>
                <label class="block text-xs font-medium mb-1.5" style="color: #6B7280;">Motivo de la reasignación</label>
                <select class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;">
                    <option>Seleccione un motivo</option>
                    <option>Carga de trabajo excesiva</option>
                    <option>Especialización en la materia</option>
                    <option>Conflicto de intereses</option>
                    <option>Rotación de procuradores</option>
                    <option>Otro</option>
                </select>
            </div>

            {{-- Casos a reasignar --}}
            <div>
                <label class="block text-xs font-medium mb-1.5" style="color: #6B7280;">Casos a reasignar</label>
                <div class="rounded-lg" style="border: 1px solid #E5E7EB;">
                    <table class="w-full text-sm">
                        <thead>
                            <tr style="background: #F9FAFB;">
                                <th class="text-left px-4 py-2.5 text-xs font-medium" style="color: #6B7280; width: 40px;">
                                    <input type="checkbox" checked class="rounded" style="border-color: #E5E7EB;">
                                </th>
                                <th class="text-left px-4 py-2.5 text-xs font-medium" style="color: #6B7280;">No. Expediente</th>
                                <th class="text-left px-4 py-2.5 text-xs font-medium" style="color: #6B7280;">Cliente</th>
                                <th class="text-left px-4 py-2.5 text-xs font-medium" style="color: #6B7280;">Tipo de proceso</th>
                                <th class="text-left px-4 py-2.5 text-xs font-medium" style="color: #6B7280;">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-t" style="border-color: #F3F4F6;">
                                <td class="px-4 py-2.5"><input type="checkbox" checked class="rounded" style="border-color: #E5E7EB;"></td>
                                <td class="px-4 py-2.5 text-sm font-medium" style="color: #2563EB;">0501-2026-00431</td>
                                <td class="px-4 py-2.5 text-sm" style="color: #111827;">Ena Elizabeth Flores Álvarez</td>
                                <td class="px-4 py-2.5 text-sm" style="color: #6B7280;">Divorcio contencioso</td>
                                <td class="px-4 py-2.5"><x-estado-badge estado="Audiencia señalada" /></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pendientes --}}
            <div>
                <label class="block text-xs font-medium mb-1.5" style="color: #6B7280;">Pendientes por realizar</label>
                <textarea rows="3" class="w-full rounded-lg px-3 py-2 text-sm outline-none transition-all" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF; resize: vertical;" onfocus="this.style.borderColor='#2563EB';" onblur="this.style.borderColor='#E5E7EB';" placeholder="Describa las actividades pendientes..."></textarea>
            </div>

            {{-- Documentos entregados --}}
            <div>
                <label class="block text-xs font-medium mb-1.5" style="color: #6B7280;">Documentos entregados</label>
                <div class="space-y-2">
                    @php
                    $docs = ['Solicitud de divorcio.pdf', 'Certificado de matrimonio.pdf', 'Constancia de trabajo.pdf', 'Fotografías (evidencia).jpg'];
                    @endphp
                    @foreach ($docs as $doc)
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" checked class="rounded" style="border-color: #E5E7EB;">
                        <span class="text-sm" style="color: #111827;">{{ $doc }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Observaciones --}}
            <div>
                <label class="block text-xs font-medium mb-1.5" style="color: #6B7280;">Observaciones adicionales</label>
                <textarea rows="2" class="w-full rounded-lg px-3 py-2 text-sm outline-none transition-all" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF; resize: vertical;" onfocus="this.style.borderColor='#2563EB';" onblur="this.style.borderColor='#E5E7EB';"></textarea>
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-end gap-3 px-6 py-4" style="border-top: 1px solid #E5E7EB;">
            <a href="{{ route('casos.show', '0501-2026-00431') }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-all"
               style="border: 1px solid #E5E7EB; color: #6B7280; background: #FFFFFF;"
               onmouseover="this.style.background='#F9FAFB';" onmouseout="this.style.background='#FFFFFF';">Cancelar</a>
            <button type="submit" class="px-5 py-2 rounded-lg text-sm font-medium transition-all"
                    style="background: #2563EB; color: white;"
                    onmouseover="this.style.background='#1d4ed8';" onmouseout="this.style.background='#2563EB';">Confirmar reasignación</button>
        </div>
    </div>
</form>
@endsection
