<?php

namespace App\Http\Controllers;

use App\Enums\RolEnum;
use App\Models\Audiencia;
use App\Models\Caso;
use App\Models\Cliente;
use App\Models\Demandado;
use App\Models\Documento;
use App\Models\Entrevista;
use App\Models\Procurador;
use App\Models\Seguimiento;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controlador para la búsqueda global typeahead.
 *
 * Busca en tiempo real sobre TODAS las entidades del sistema:
 * Casos, Clientes, Demandados, Procuradores, Audiencias,
 * Documentos, Entrevistas y Seguimientos.
 *
 * Respeta el filtrado por procurador: si el usuario es Procurador,
 * solo ve resultados vinculados a sus propios casos.
 */
class SearchController extends Controller
{
    /**
     * Ejecuta la búsqueda global y retorna resultados categorizados.
     *
     * @param  Request  $request  Parámetros: q (término de búsqueda)
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        $q = trim($request->validate(['q' => 'required|string|max:100'])['q']);

        if ($q === '') {
            return response()->json(['results' => []]);
        }

        $user = $request->user();
        $esProcurador = RolEnum::equals($user->rol?->rol_nombre, RolEnum::PROCURADOR);
        $procuradorId = $esProcurador ? $user->procurador_id : null;

        $results = [];
        $limit = 8; // máximos por entidad para mantener rápido el typeahead

        // ═══════════════════════════════════════════════
        // 1. CASOS — por expediente, juzgado o parte
        // ═══════════════════════════════════════════════
        $casosQuery = Caso::select('casos.caso_id', 'casos.caso_numero_expediente', 'casos.caso_juzgado')
            ->where(function ($qry) use ($q) {
                $qry->where('caso_numero_expediente', 'like', "%{$q}%")
                    ->orWhere('caso_juzgado', 'like', "%{$q}%")
                    ->orWhere('caso_parte_representada', 'like', "%{$q}%");
            });

        if ($esProcurador && $procuradorId) {
            $casosQuery->where('procurador_id', $procuradorId);
        }

        foreach ($casosQuery->limit($limit)->get() as $caso) {
            $sub = $caso->caso_juzgado ? "Juzgado: {$caso->caso_juzgado}" : 'Expediente';
            $results[] = [
                'type' => 'Caso',
                'label' => $caso->caso_numero_expediente,
                'sub' => $sub,
                'url' => route('casos.show', $caso->caso_numero_expediente),
            ];
        }

        // ═══════════════════════════════════════════════
        // 2. CLIENTES — por nombre, apellido o DNI
        // ═══════════════════════════════════════════════
        $clientesQuery = Cliente::select('clientes.cliente_id', 'clientes.cliente_nombre', 'clientes.cliente_apellido', 'clientes.cliente_dni')
            ->where(function ($qry) use ($q) {
                $qry->where('cliente_nombre', 'like', "%{$q}%")
                    ->orWhere('cliente_apellido', 'like', "%{$q}%")
                    ->orWhere('cliente_dni', 'like', "%{$q}%");
            })
            ->where('cliente_estado', 'activo');

        if ($esProcurador && $procuradorId) {
            $clientesQuery->whereHas('casos', fn ($qry) => $qry->where('procurador_id', $procuradorId));
        }

        foreach ($clientesQuery->limit($limit)->get() as $cliente) {
            $results[] = [
                'type' => 'Cliente',
                'label' => "{$cliente->cliente_nombre} {$cliente->cliente_apellido}",
                'sub' => "DNI: {$cliente->cliente_dni}",
                'url' => route('clientes.show', $cliente->cliente_dni),
            ];
        }

        // ═══════════════════════════════════════════════
        // 3. DEMANDADOS — por nombre, apellido o DNI
        // ═══════════════════════════════════════════════
        $demandadosQuery = Demandado::select('demandados.demandado_id', 'demandados.demandado_nombre', 'demandados.demandado_apellido', 'demandados.demandado_dni')
            ->where(function ($qry) use ($q) {
                $qry->where('demandado_nombre', 'like', "%{$q}%")
                    ->orWhere('demandado_apellido', 'like', "%{$q}%")
                    ->orWhere('demandado_dni', 'like', "%{$q}%");
            })
            ->where('demandado_estado', 'activo');

        if ($esProcurador && $procuradorId) {
            $demandadosQuery->whereHas('casos', fn ($qry) => $qry->where('procurador_id', $procuradorId));
        }

        foreach ($demandadosQuery->limit($limit)->get() as $demandado) {
            $results[] = [
                'type' => 'Demandado',
                'label' => "{$demandado->demandado_nombre} {$demandado->demandado_apellido}",
                'sub' => "DNI: {$demandado->demandado_dni}",
                'url' => route('demandados.show', $demandado->demandado_dni),
            ];
        }

        // ═══════════════════════════════════════════════
        // 4. PROCURADORES — por nombre, apellido, DNI, carnet o email
        // ═══════════════════════════════════════════════
        $procuradoresQuery = Procurador::select(
            'procuradores.procurador_id',
            'procuradores.procurador_nombre',
            'procuradores.procurador_apellido',
            'procuradores.procurador_dni',
            'procuradores.procurador_carnet'
        )->where(function ($qry) use ($q) {
            $qry->where('procurador_nombre', 'like', "%{$q}%")
                ->orWhere('procurador_apellido', 'like', "%{$q}%")
                ->orWhere('procurador_dni', 'like', "%{$q}%")
                ->orWhere('procurador_carnet', 'like', "%{$q}%")
                ->orWhere('procurador_email', 'like', "%{$q}%");
        })
            ->where('procurador_estado', 'activo');

        foreach ($procuradoresQuery->limit($limit)->get() as $procurador) {
            $sub = $procurador->procurador_carnet
                ? "Carnet: {$procurador->procurador_carnet}"
                : "DNI: {$procurador->procurador_dni}";
            $results[] = [
                'type' => 'Procurador',
                'label' => "{$procurador->procurador_nombre} {$procurador->procurador_apellido}",
                'sub' => $sub,
                'url' => route('procuradores.show', $procurador->procurador_dni),
            ];
        }

        // ═══════════════════════════════════════════════
        // 5. AUDIENCIAS — por tipo, juzgado o fecha (con el caso relacionado)
        // ═══════════════════════════════════════════════
        $audienciasQuery = Audiencia::select(
            'audiencias.audiencia_id',
            'audiencias.caso_id',
            'audiencias.audiencia_fecha',
            'audiencias.audiencia_tipo',
            'audiencias.audiencia_juzgado',
        )->with('caso:caso_id,caso_numero_expediente')
            ->where(function ($qry) use ($q) {
                $qry->where('audiencia_tipo', 'like', "%{$q}%")
                    ->orWhere('audiencia_juzgado', 'like', "%{$q}%")
                    ->orWhere('audiencia_fecha', 'like', "%{$q}%");
            });

        if ($esProcurador && $procuradorId) {
            $audienciasQuery->whereHas('caso', fn ($qry) => $qry->where('procurador_id', $procuradorId));
        }

        foreach ($audienciasQuery->limit($limit)->get() as $audiencia) {
            $expediente = $audiencia->caso?->caso_numero_expediente ?? 'N/A';
            $results[] = [
                'type' => 'Audiencia',
                'label' => "{$audiencia->audiencia_tipo} — {$expediente}",
                'sub' => $audiencia->audiencia_juzgado
                    ? "{$audiencia->audiencia_fecha} · {$audiencia->audiencia_juzgado}"
                    : (string) $audiencia->audiencia_fecha,
                'url' => route('casos.show', $expediente),
            ];
        }

        // ═══════════════════════════════════════════════
        // 6. DOCUMENTOS — por nombre o descripción
        // ═══════════════════════════════════════════════
        $documentosQuery = Documento::select(
            'documentos.documento_id',
            'documentos.caso_id',
            'documentos.documento_nombre',
            'documentos.documento_descripcion',
        )->with('caso:caso_id,caso_numero_expediente')
            ->where(function ($qry) use ($q) {
                $qry->where('documento_nombre', 'like', "%{$q}%")
                    ->orWhere('documento_descripcion', 'like', "%{$q}%");
            })
            ->where('documento_estado', 'activo');

        if ($esProcurador && $procuradorId) {
            $documentosQuery->whereHas('caso', fn ($qry) => $qry->where('procurador_id', $procuradorId));
        }

        foreach ($documentosQuery->limit($limit)->get() as $documento) {
            $expediente = $documento->caso?->caso_numero_expediente ?? 'N/A';
            $results[] = [
                'type' => 'Documento',
                'label' => $documento->documento_nombre,
                'sub' => "Caso: {$expediente}" . ($documento->documento_descripcion ? " · {$documento->documento_descripcion}" : ''),
                'url' => route('casos.show', $expediente),
            ];
        }

        // ═══════════════════════════════════════════════
        // 7. ENTREVISTAS — por relación de hechos u observaciones
        // ═══════════════════════════════════════════════
        $entrevistasQuery = Entrevista::select(
            'entrevistas.entrevista_id',
            'entrevistas.caso_id',
            'entrevistas.entrevista_fecha',
            'entrevistas.entrevista_relacion_hechos',
        )->with('caso:caso_id,caso_numero_expediente')
            ->where(function ($qry) use ($q) {
                $qry->where('entrevista_relacion_hechos', 'like', "%{$q}%")
                    ->orWhere('entrevista_observaciones', 'like', "%{$q}%");
            })
            ->where('entrevista_estado', 'activo');

        if ($esProcurador && $procuradorId) {
            $entrevistasQuery->whereHas('caso', fn ($qry) => $qry->where('procurador_id', $procuradorId));
        }

        foreach ($entrevistasQuery->limit($limit)->get() as $entrevista) {
            $expediente = $entrevista->caso?->caso_numero_expediente ?? 'N/A';
            $hechos = $entrevista->entrevista_relacion_hechos
                ? substr($entrevista->entrevista_relacion_hechos, 0, 60) . (strlen($entrevista->entrevista_relacion_hechos) > 60 ? '…' : '')
                : 'Sin detalle';
            $results[] = [
                'type' => 'Entrevista',
                'label' => "Entrevista {$entrevista->entrevista_fecha} — {$expediente}",
                'sub' => $hechos,
                'url' => route('casos.show', $expediente),
            ];
        }

        // ═══════════════════════════════════════════════
        // 8. SEGUIMIENTOS — por descripción
        // ═══════════════════════════════════════════════
        $seguimientosQuery = Seguimiento::select(
            'seguimientos.seguimiento_id',
            'seguimientos.caso_id',
            'seguimientos.seguimiento_fecha',
            'seguimientos.seguimiento_tipo',
            'seguimientos.seguimiento_descripcion',
        )->with('caso:caso_id,caso_numero_expediente')
            ->where('seguimiento_descripcion', 'like', "%{$q}%")
            ->where('seguimiento_estado', 'activo');

        if ($esProcurador && $procuradorId) {
            $seguimientosQuery->whereHas('caso', fn ($qry) => $qry->where('procurador_id', $procuradorId));
        }

        foreach ($seguimientosQuery->limit($limit)->get() as $seguimiento) {
            $expediente = $seguimiento->caso?->caso_numero_expediente ?? 'N/A';
            $desc = substr($seguimiento->seguimiento_descripcion, 0, 60)
                . (strlen($seguimiento->seguimiento_descripcion) > 60 ? '…' : '');
            $results[] = [
                'type' => 'Seguimiento',
                'label' => "{$seguimiento->seguimiento_tipo} — {$expediente}",
                'sub' => $desc,
                'url' => route('casos.show', $expediente),
            ];
        }

        return response()->json(['results' => $results]);
    }
}
