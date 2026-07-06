{{--
    Vista: seguimientos/store
    Propósito: Formulario parcial para agregar un nuevo seguimiento a la bitácora de un caso. Incluye selector de tipo de evento, plantillas rápidas y campo de descripción. Se incluye en la vista casos/show.
    Variables: $caso (modelo Caso, para obtener caso_id y asociar el seguimiento)
--}}
<div class="mb-8 p-6 rounded-xl border border-gray-200 shadow-sm" style="background: #FFFFFF;" x-data="{ 
    descripcion: '',
    tipo: 'Trámite',
    aplicarPlantilla(texto, tipoSujeto) {
        this.descripcion = texto;
        this.tipo = tipoSujeto;
    }
}">
    <div class="flex items-center gap-3 mb-4">
        <div class="flex items-center justify-center w-8 height-8 rounded-full bg-blue-100 text-blue-600 font-semibold text-sm">
            {{ strtoupper(substr(Auth::user()->usuario_nombre ?? auth()->user()->name ?? 'U', 0, 1)) }}
        </div>
        <div>
            <h4 class="text-sm font-semibold text-gray-900">Agregar actualización a la bitácora</h4>
            <p class="text-xs text-gray-500">Publicando como: <span class="font-medium text-gray-700">{{ Auth::user()->usuario_nombre ?? 'Usuario Autenticado' }}</span></p>
        </div>
    </div>
    
    <form action="{{ route('seguimientos.store', $caso->caso_id) }}" method="POST" class="space-y-4">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            {{-- Selector de Tipo de Seguimiento --}}
            <div class="md:col-span-1">
                <label for="seguimiento_tipo" class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wider">Tipo de Evento</label>
                <select id="seguimiento_tipo" name="seguimiento_tipo" x-model="tipo" required
                        class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2.5 bg-white border text-gray-700">
                    <option value="Presentación">Presentación</option>
                    <option value="Admisión">Admisión</option>
                    <option value="Notificación">Notificación</option>
                    <option value="Audiencia">Audiencia</option>
                    <option value="Trámite">Trámite</option>
                    <option value="Inicio">Inicio desde cero</option>
                </select>
            </div>

            {{-- Sección de Plantillas Rápidas --}}
            <div class="md:col-span-3">
                <span class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wider">Acciones / Plantillas Rápidas</span>
                <div class="flex flex-wrap gap-2">
                    {{-- Presentado al Juzgado --}}
                    <button type="button" @click="aplicarPlantilla('YA FUE PRESENTADO AL JUZGADO', 'Presentación')" 
                            class="text-xs bg-gray-50 border border-gray-300 text-gray-700 px-3 py-1.5 rounded-lg hover:bg-gray-100 hover:border-gray-400 transition-all inline-flex items-center font-medium">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mr-1.5 text-gray-500"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        Presentado al Juzgado
                    </button>

                    {{-- Admitida --}}
                    <button type="button" @click="aplicarPlantilla('ADMITIDA EN FECHA {{ now()->format('d/m/Y') }}', 'Admisión')" 
                            class="text-xs bg-gray-50 border border-gray-300 text-gray-700 px-3 py-1.5 rounded-lg hover:bg-gray-100 hover:border-gray-400 transition-all inline-flex items-center font-medium">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mr-1.5 text-green-600"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        Admitida (Hoy)
                    </button>

                    {{-- Espera Audiencia --}}
                    <button type="button" @click="aplicarPlantilla('EN ESPERA DE SEÑALAMIENTO DE AUDIENCIA POR PARTE DEL JUZGADO', 'Audiencia')" 
                            class="text-xs bg-gray-50 border border-gray-300 text-gray-700 px-3 py-1.5 rounded-lg hover:bg-gray-100 hover:border-gray-400 transition-all inline-flex items-center font-medium">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mr-1.5 text-amber-500"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        Espera Audiencia
                    </button>

                    {{-- Emplazado --}}
                    <button type="button" @click="aplicarPlantilla('YA FUE EMPLAZADO EL DEMANDANTE POR PARTE DEL JUZGADO', 'Notificación')" 
                            class="text-xs bg-gray-50 border border-gray-300 text-gray-700 px-3 py-1.5 rounded-lg hover:bg-gray-100 hover:border-gray-400 transition-all inline-flex items-center font-medium">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mr-1.5 text-blue-500"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                        Emplazado
                    </button>

                    {{-- Caso Desde Cero --}}
                    <button type="button" @click="aplicarPlantilla('CASO QUE SE LLEVARA DESDE CERO', 'Inicio')" 
                            class="text-xs bg-gray-50 border border-gray-300 text-gray-700 px-3 py-1.5 rounded-lg hover:bg-gray-100 hover:border-gray-400 transition-all inline-flex items-center font-medium">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mr-1.5 text-indigo-500"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
                        Desde Cero
                    </button>
                </div>
            </div>
        </div>

        {{-- Entrada de Texto de la Descripción --}}
        <div class="relative mt-2">
            <textarea id="seguimiento_descripcion" name="seguimiento_descripcion" rows="3" required x-model="descripcion"
                      placeholder="Escribe los detalles importantes de la actuación judicial..."
                      class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 bg-white border text-gray-800 placeholder-gray-400 resize-none"></textarea>
        </div>

        {{-- Acciones del Formulario --}}
        <div class="flex justify-between items-center pt-1">
            <p class="text-xs text-gray-400 italic">Los seguimientos guardados se publican con la fecha de hoy de forma automática.</p>
            <button type="submit" class="px-5 py-2 text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 active:bg-blue-800 transition shadow-sm inline-flex items-center gap-1.5">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                Publicar en Bitácora
            </button>
        </div>
    </form>
</div>