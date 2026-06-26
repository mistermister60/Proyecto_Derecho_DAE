@props(['titulo', 'valor', 'icono', 'color' => '#2563EB', 'subtexto' => null])

<div class="rounded-xl p-5 transition-shadow duration-200"
     style="background: #FFFFFF; border: 1px solid #E5E7EB;"
     onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.06)';"
     onmouseout="this.style.boxShadow='none';">
    <div class="flex items-start justify-between">
        <div>
            <p class="text-sm font-medium" style="color: #6B7280;">{{ $titulo }}</p>
            <p class="text-2xl font-bold mt-1" style="color: #111827;">{{ $valor }}</p>
            @if ($subtexto)
                <p class="text-xs mt-1" style="color: #9CA3AF;">{{ $subtexto }}</p>
            @endif
        </div>
        @if ($icono)
            <div class="flex items-center justify-center rounded-lg p-2.5" style="background: {{ $color }}15; color: {{ $color }};">
                {!! $icono !!}
            </div>
        @endif
    </div>
</div>
