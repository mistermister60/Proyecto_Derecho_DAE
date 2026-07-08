{{--
    Componente: kpi-card
    Propósito: Tarjeta de indicador clave (KPI) para el dashboard. Muestra título, valor numérico, icono SVG y texto opcional.
    Props: $titulo (nombre del indicador), $valor (número), $icono (SVG), $color (hex color), $subtexto (texto secundario)
--}}
@props(['titulo', 'valor', 'icono', 'color' => '#2563EB', 'subtexto' => null])

<div class="rounded-2xl p-6 transition-all duration-300 hover:-translate-y-1"
     style="background: #FFFFFF; border: 1px solid #E5E7EB;"
     onmouseover="this.style.boxShadow='0 10px 25px rgba(0,0,0,0.08)';"
     onmouseout="this.style.boxShadow='none';">

    <div class="flex items-center justify-between">
        <div>
            <p class="text-xs uppercase tracking-wider font-semibold"
               style="color: #6B7280;">
                {{ $titulo }}
            </p>

            <p class="text-3xl font-extrabold tracking-tight mt-2"
               style="color: #111827;">
                {{ $valor }}
            </p>

            @if ($subtexto)
                <p class="text-xs mt-2"
                   style="color: #9CA3AF;">
                    {{ $subtexto }}
                </p>
            @endif
        </div>

        @if ($icono)
            <div class="flex items-center justify-center rounded-xl p-3 transition-all duration-300"
                 style="background: {{ $color }}15; color: {{ $color }};">
                {!! $icono !!}
            </div>
        @endif
    </div>
</div>