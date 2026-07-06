{{--
    Componente: estado-badge
    Propósito: Badge visual con color y punto indicador según el estado del caso. Los colores están mapeados en un array interno.
    Props: $estado (string: Activo, Inactivo, Entrevista, Admitido, Cerrado, etc.)
--}}
@props(['estado'])

@php
$colors = [
    'Activo' => ['bg' => '#DCFCE7', 'text' => '#166534', 'dot' => '#16A34A'],
    'Inactivo' => ['bg' => '#FEE2E2', 'text' => '#991B1B', 'dot' => '#DC2626'],
    'Entrevista' => ['bg' => '#F3F4F6', 'text' => '#6B7280', 'dot' => '#9CA3AF'],
    'Admitido' => ['bg' => '#EFF6FF', 'text' => '#1D4ED8', 'dot' => '#60A5FA'],
    'Poder conferido' => ['bg' => '#DBEAFE', 'text' => '#1E40AF', 'dot' => '#3B82F6'],
    'Presentado al juzgado' => ['bg' => '#EFF6FF', 'text' => '#1E40AF', 'dot' => '#2563EB'],
    'Admitido por el juzgado' => ['bg' => '#DBEAFE', 'text' => '#1E3A8A', 'dot' => '#1D4ED8'],
    'Audiencia señalada' => ['bg' => '#FEF3C7', 'text' => '#B45309', 'dot' => '#F59E0B'],
    'En sentencia' => ['bg' => '#FFFBEB', 'text' => '#92400E', 'dot' => '#D97706'],
    'Cerrado' => ['bg' => '#DCFCE7', 'text' => '#166534', 'dot' => '#16A34A'],
    'Inadmisible' => ['bg' => '#FEF2F2', 'text' => '#991B1B', 'dot' => '#EF4444'],
    'Reasignado' => ['bg' => '#F3E8FF', 'text' => '#7E22CE', 'dot' => '#A855F7'],
    'Atrasado' => ['bg' => '#FEE2E2', 'text' => '#991B1B', 'dot' => '#DC2626'],
];

$c = $colors[$estado] ?? ['bg' => '#F3F4F6', 'text' => '#6B7280', 'dot' => '#9CA3AF'];
@endphp

<span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium"
      style="background: {{ $c['bg'] }}; color: {{ $c['text'] }}; border: 1px solid #E5E7EB;">
    <span class="rounded-full" style="width: 6px; height: 6px; background: {{ $c['dot'] }}; display: inline-block; flex-shrink: 0;"></span>
    {{ $estado }}
</span>
