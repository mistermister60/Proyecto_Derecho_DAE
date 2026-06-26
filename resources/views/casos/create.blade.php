@extends('layouts.app')

@section('title', 'Nuevo caso')

@section('content')
<form class="space-y-6">
    {{-- Sección 1: Datos del cliente --}}
    <div class="rounded-xl p-5" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
        <h3 class="text-sm font-semibold mb-4" style="color: #111827;">1. Datos del cliente</h3>
        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-medium mb-1.5" style="color: #6B7280;">Nombre completo</label>
                <input type="text" class="w-full rounded-lg px-3 py-2 text-sm outline-none transition-all" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;" onfocus="this.style.borderColor='#2563EB';" onblur="this.style.borderColor='#E5E7EB';" placeholder="Nombres y apellidos">
            </div>
            <div>
                <label class="block text-xs font-medium mb-1.5" style="color: #6B7280;">Número de identidad</label>
                <input type="text" class="w-full rounded-lg px-3 py-2 text-sm outline-none transition-all" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;" onfocus="this.style.borderColor='#2563EB';" onblur="this.style.borderColor='#E5E7EB';" placeholder="0000-0000-00000">
            </div>
            <div>
                <label class="block text-xs font-medium mb-1.5" style="color: #6B7280;">Estado civil</label>
                <select class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;">
                    <option>Soltero/a</option>
                    <option>Casado/a</option>
                    <option>Divorciado/a</option>
                    <option>Viudo/a</option>
                    <option>Unión libre</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium mb-1.5" style="color: #6B7280;">Teléfono</label>
                <input type="text" class="w-full rounded-lg px-3 py-2 text-sm outline-none transition-all" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;" onfocus="this.style.borderColor='#2563EB';" onblur="this.style.borderColor='#E5E7EB';">
            </div>
            <div>
                <label class="block text-xs font-medium mb-1.5" style="color: #6B7280;">Profesión / Oficio</label>
                <input type="text" class="w-full rounded-lg px-3 py-2 text-sm outline-none transition-all" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;" onfocus="this.style.borderColor='#2563EB';" onblur="this.style.borderColor='#E5E7EB';">
            </div>
            <div>
                <label class="block text-xs font-medium mb-1.5" style="color: #6B7280;">Salario mensual</label>
                <input type="text" class="w-full rounded-lg px-3 py-2 text-sm outline-none transition-all" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;" onfocus="this.style.borderColor='#2563EB';" onblur="this.style.borderColor='#E5E7EB';" placeholder="L.">
            </div>
            <div class="col-span-3">
                <label class="block text-xs font-medium mb-1.5" style="color: #6B7280;">Dirección</label>
                <input type="text" class="w-full rounded-lg px-3 py-2 text-sm outline-none transition-all" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;" onfocus="this.style.borderColor='#2563EB';" onblur="this.style.borderColor='#E5E7EB';">
            </div>
        </div>
    </div>

    {{-- Sección 2: Relación de hechos + Tipo de trámite --}}
    <div class="rounded-xl p-5" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
        <h3 class="text-sm font-semibold mb-4" style="color: #111827;">2. Relación de hechos y tipo de trámite</h3>
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-xs font-medium mb-1.5" style="color: #6B7280;">Tipo de trámite</label>
                <select class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;">
                    <option>Seleccione un trámite</option>
                    <option>Disolución por mutuo acuerdo</option>
                    <option>Divorcio contencioso</option>
                    <option>Demanda de alimentos</option>
                    <option>Revisión de demanda de alimentos</option>
                    <option>Reconocimiento forzoso de paternidad</option>
                    <option>Solicitud de ejecución forzosa</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium mb-1.5" style="color: #6B7280;">Parte representada</label>
                <select class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;">
                    <option>Demandante</option>
                    <option>Demandada</option>
                    <option>Ambas partes</option>
                </select>
            </div>
        </div>
        <div>
            <label class="block text-xs font-medium mb-1.5" style="color: #6B7280;">Relación de hechos</label>
            <textarea rows="5" class="w-full rounded-lg px-3 py-2 text-sm outline-none transition-all" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF; resize: vertical;" onfocus="this.style.borderColor='#2563EB';" onblur="this.style.borderColor='#E5E7EB';" placeholder="Describa los hechos..."></textarea>
        </div>
    </div>

    {{-- Sección 3: Datos del demandado --}}
    <div class="rounded-xl p-5" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
        <h3 class="text-sm font-semibold mb-4" style="color: #111827;">3. Datos del demandado / contraparte</h3>
        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-medium mb-1.5" style="color: #6B7280;">Nombre completo</label>
                <input type="text" class="w-full rounded-lg px-3 py-2 text-sm outline-none transition-all" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;" onfocus="this.style.borderColor='#2563EB';" onblur="this.style.borderColor='#E5E7EB';">
            </div>
            <div>
                <label class="block text-xs font-medium mb-1.5" style="color: #6B7280;">Número de identidad</label>
                <input type="text" class="w-full rounded-lg px-3 py-2 text-sm outline-none transition-all" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;" onfocus="this.style.borderColor='#2563EB';" onblur="this.style.borderColor='#E5E7EB';">
            </div>
            <div>
                <label class="block text-xs font-medium mb-1.5" style="color: #6B7280;">Teléfono</label>
                <input type="text" class="w-full rounded-lg px-3 py-2 text-sm outline-none transition-all" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;" onfocus="this.style.borderColor='#2563EB';" onblur="this.style.borderColor='#E5E7EB';">
            </div>
            <div class="col-span-3">
                <label class="block text-xs font-medium mb-1.5" style="color: #6B7280;">Dirección</label>
                <input type="text" class="w-full rounded-lg px-3 py-2 text-sm outline-none transition-all" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;" onfocus="this.style.borderColor='#2563EB';" onblur="this.style.borderColor='#E5E7EB';">
            </div>
        </div>
    </div>

    {{-- Sección 4: Información del consultorio --}}
    <div class="rounded-xl p-5" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
        <h3 class="text-sm font-semibold mb-4" style="color: #111827;">4. Información para el consultorio</h3>
        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-medium mb-1.5" style="color: #6B7280;">Admisibilidad</label>
                <select class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;">
                    <option>Seleccione</option>
                    <option>Admisible</option>
                    <option>Inadmisible</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium mb-1.5" style="color: #6B7280;">Procurador entrevistador</label>
                <select class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;">
                    <option>Seleccione un procurador</option>
                    <option>Iris Lizeth Rodríguez (0257-26)</option>
                    <option>Franklyn Geovanny Salgado (0312-26)</option>
                    <option>Indira Pauleth Galindo (0189-26)</option>
                    <option>Carlos Alberto Brizuela (0421-26)</option>
                    <option>Ena Elizabeth Flores (0098-26)</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium mb-1.5" style="color: #6B7280;">Juzgado</label>
                <select class="w-full rounded-lg px-3 py-2 text-sm outline-none" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;">
                    <option>Seleccione</option>
                    <option>J-3</option>
                    <option>J-7</option>
                    <option>J-8</option>
                </select>
            </div>
        </div>
        <div class="mt-4">
            <label class="block text-xs font-medium mb-1.5" style="color: #6B7280;">Observaciones del Director</label>
            <textarea rows="3" class="w-full rounded-lg px-3 py-2 text-sm outline-none transition-all" style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF; resize: vertical;" onfocus="this.style.borderColor='#2563EB';" onblur="this.style.borderColor='#E5E7EB';"></textarea>
        </div>
    </div>

    {{-- Botones --}}
    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('casos.index') }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-all"
           style="border: 1px solid #E5E7EB; color: #6B7280; background: #FFFFFF;"
           onmouseover="this.style.background='#F9FAFB';" onmouseout="this.style.background='#FFFFFF';">Cancelar</a>
        <button type="button" class="px-4 py-2 rounded-lg text-sm font-medium transition-all"
                style="border: 1px solid #E5E7EB; color: #6B7280; background: #FFFFFF;"
                onmouseover="this.style.background='#F9FAFB';" onmouseout="this.style.background='#FFFFFF';">Guardar borrador</button>
        <button type="submit" class="px-5 py-2 rounded-lg text-sm font-medium transition-all"
                style="background: #2563EB; color: white;"
                onmouseover="this.style.background='#1d4ed8';" onmouseout="this.style.background='#2563EB';">Registrar caso</button>
    </div>
</form>
@endsection
