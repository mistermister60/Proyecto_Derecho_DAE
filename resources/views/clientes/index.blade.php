@extends('layouts.app')

@section('title', 'Clientes')

@section('content')
<div x-data="{ search: '' }">
    <div class="flex items-center justify-between mb-5">
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#9CA3AF" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" x-model="search" placeholder="Buscar por nombre o identidad..."
                   class="pl-9 pr-3 py-1.5 rounded-lg text-sm outline-none"
                   style="border: 1px solid #E5E7EB; color: #111827; width: 300px; background: #FFFFFF;"
                   onfocus="this.style.borderColor='#2563EB';"
                   onblur="this.style.borderColor='#E5E7EB';">
        </div>
    </div>

    <x-tabla :encabezados="['Cliente', 'Identidad', 'Teléfono', 'Estado civil', 'Casos activos', 'Acción']">
        @php
        $clientes = [
            ['nombre' => 'Ena Elizabeth Flores Álvarez', 'identidad' => '0501-1990-04521', 'tel' => '9876-5432', 'civil' => 'Casada', 'activos' => 2],
            ['nombre' => 'Franklyn Geovanny Salgado Pineda', 'identidad' => '0501-1992-01234', 'tel' => '9654-3210', 'civil' => 'Soltero', 'activos' => 1],
            ['nombre' => 'Indira Pauleth Galindo Vásquez', 'identidad' => '0501-1995-07890', 'tel' => '9234-5678', 'civil' => 'Soltera', 'activos' => 1],
            ['nombre' => 'Bernarda Aracely Paz Guzmán', 'identidad' => '0501-1985-03456', 'tel' => '9567-8901', 'civil' => 'Divorciada', 'activos' => 1],
            ['nombre' => 'Carlos Alberto Brizuela Zamora', 'identidad' => '0501-1988-05678', 'tel' => '9345-6789', 'civil' => 'Casado', 'activos' => 2],
            ['nombre' => 'María José Reyes Padilla', 'identidad' => '0501-1993-06789', 'tel' => '9789-0123', 'civil' => 'Soltera', 'activos' => 0],
            ['nombre' => 'Pedro Antonio Mejía López', 'identidad' => '0501-1980-08901', 'tel' => '9901-2345', 'civil' => 'Casado', 'activos' => 1],
            ['nombre' => 'Ana Cecilia García Hernández', 'identidad' => '0501-1991-09012', 'tel' => '9012-3456', 'civil' => 'Soltera', 'activos' => 1],
        ];
        @endphp
        @foreach ($clientes as $cliente)
        <tr class="transition-colors border-t" style="border-color: #F3F4F6; cursor: pointer;" onmouseover="this.style.background='#F9FAFB';" onmouseout="this.style.background='transparent';" onclick="window.location='{{ route('clientes.show', $cliente['identidad']) }}'">
            <td class="px-4 py-3">
                <div class="flex items-center gap-3">
                    <div class="flex items-center justify-center rounded-full shrink-0" style="width: 32px; height: 32px; background: #1E3A5F; color: white; font-size: 11px; font-weight: 600;">
                        {{ implode('', array_map(fn($n) => $n[0], explode(' ', $cliente['nombre']))) }}
                    </div>
                    <span class="text-sm font-medium" style="color: #111827;">{{ $cliente['nombre'] }}</span>
                </div>
            </td>
            <td class="px-4 py-3 text-sm" style="color: #6B7280;">{{ $cliente['identidad'] }}</td>
            <td class="px-4 py-3 text-sm" style="color: #6B7280;">{{ $cliente['tel'] }}</td>
            <td class="px-4 py-3 text-sm" style="color: #6B7280;">{{ $cliente['civil'] }}</td>
            <td class="px-4 py-3">
                <span class="text-sm font-medium" style="color: {{ $cliente['activos'] > 0 ? '#2563EB' : '#9CA3AF' }};">{{ $cliente['activos'] }}</span>
            </td>
            <td class="px-4 py-3">
                <a href="{{ route('clientes.show', $cliente['identidad']) }}" class="text-xs font-medium transition-colors" style="color: #2563EB;" onmouseover="this.style.color='#1d4ed8';" onmouseout="this.style.color='#2563EB';">Ver ficha</a>
            </td>
        </tr>
        @endforeach
    </x-tabla>
</div>
@endsection
