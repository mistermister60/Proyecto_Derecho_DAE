@props(['encabezados' => [], 'sinDatos' => 'No hay datos disponibles.'])

<div class="w-full overflow-x-auto rounded-xl" style="border: 1px solid #E5E7EB; background: #FFFFFF;">
    <table class="w-full text-sm">
        <thead>
            <tr style="background: #F9FAFB;">
                @foreach ($encabezados as $enc)
                    <th scope="col" class="text-left px-4 py-3 font-medium text-xs uppercase tracking-wider" style="color: #6B7280;">{{ $enc }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody style="color: #111827;">
            @if (trim((string) $slot) !== '')
                {{ $slot }}
            @else
                <tr>
                    <td colspan="{{ count($encabezados) }}" class="px-4 py-8 text-center text-sm" style="color: #9CA3AF;">
                        {{ $sinDatos }}
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
