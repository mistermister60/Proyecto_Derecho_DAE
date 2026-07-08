<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Seguimiento de Caso</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { color: #1a3a5c; font-size: 16px; text-align: center; }
        h2 { color: #333; font-size: 14px; border-bottom: 2px solid #1a3a5c; padding-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th { background: #1a3a5c; color: white; padding: 8px; text-align: left; }
        td { padding: 6px; border-bottom: 1px solid #ddd; }
        .info { margin: 5px 0; }
        .label { font-weight: bold; }
        .footer { text-align: center; margin-top: 30px; color: #666; font-size: 10px; }
    </style>
</head>
<body>
    <h1>Universidad San Pedro Sula</h1>
    <h1 style="margin-top: 0;">Consultorio Jurídico DAE</h1>

    <h2>Datos del Caso</h2>
    <p class="info"><span class="label">Expediente:</span> {{ $caso->caso_numero_expediente }}</p>
    <p class="info"><span class="label">Cliente:</span> {{ $caso->cliente->nombre_completo ?? 'N/A' }}</p>
    <p class="info"><span class="label">Procurador:</span> {{ $caso->procurador->nombre_completo ?? 'N/A' }}</p>
    <p class="info"><span class="label">Estado:</span> {{ $caso->estado->estado_nombre ?? 'N/A' }}</p>
    <p class="info"><span class="label">Fecha de creación:</span> {{ $caso->created_at->format('d/m/Y') }}</p>

    <h2>Seguimientos</h2>

    @if($caso->seguimientos->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Descripción</th>
                    <th>Registrado por</th>
                </tr>
            </thead>
            <tbody>
                @foreach($caso->seguimientos as $seg)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($seg->seguimiento_fecha)->format('d/m/Y') }}</td>
                    <td>{{ $seg->seguimiento_descripcion }}</td>
                    <td>{{ $seg->usuario->usuario_nombre ?? 'Sistema' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="color: #999;">No hay seguimientos registrados para este caso.</p>
    @endif

    <div class="footer">
        Documento generado el {{ now()->format('d/m/Y H:i:s') }} - Sistema DAE
    </div>
</body>
</html>