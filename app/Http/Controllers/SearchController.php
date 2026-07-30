<?php

namespace App\Http\Controllers;

use App\Enums\RolEnum;
use App\Models\Caso;
use App\Models\Cliente;
use App\Models\Demandado;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controlador para la búsqueda global typeahead.
 *
 * Busca en tiempo real sobre casos (expediente), clientes y demandados.
 * Respeta el filtrado por procurador: si el usuario es Procurador, solo
 * ve resultados de sus propios casos.
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

        // ─── Casos por número de expediente ───
        $casosQuery = Caso::select('casos.caso_id', 'casos.caso_numero_expediente')
            ->where('caso_numero_expediente', 'like', "%{$q}%");

        if ($esProcurador && $procuradorId) {
            $casosQuery->where('procurador_id', $procuradorId);
        }

        $casos = $casosQuery->limit(5)->get();

        foreach ($casos as $caso) {
            $results[] = [
                'type' => 'Caso',
                'type_icon' => 'folder',
                'label' => $caso->caso_numero_expediente,
                'sub' => 'Expediente',
                'url' => route('casos.show', $caso->caso_numero_expediente),
            ];
        }

        // ─── Clientes por nombre/apellido/DNI ───
        $clientesQuery = Cliente::select('clientes.cliente_id', 'clientes.cliente_nombre', 'clientes.cliente_apellido', 'clientes.cliente_dni')
            ->where(function ($query) use ($q) {
                $query->where('cliente_nombre', 'like', "%{$q}%")
                    ->orWhere('cliente_apellido', 'like', "%{$q}%")
                    ->orWhere('cliente_dni', 'like', "%{$q}%");
            })
            ->where('cliente_estado', 'activo');

        if ($esProcurador && $procuradorId) {
            $clientesQuery->whereHas('casos', function ($query) use ($procuradorId) {
                $query->where('procurador_id', $procuradorId);
            });
        }

        $clientes = $clientesQuery->limit(5)->get();

        foreach ($clientes as $cliente) {
            $results[] = [
                'type' => 'Cliente',
                'type_icon' => 'user',
                'label' => "{$cliente->cliente_nombre} {$cliente->cliente_apellido}",
                'sub' => "DNI: {$cliente->cliente_dni}",
                'url' => route('clientes.show', $cliente->cliente_dni),
            ];
        }

        // ─── Demandados por nombre/apellido/DNI ───
        $demandadosQuery = Demandado::select('demandados.demandado_id', 'demandados.demandado_nombre', 'demandados.demandado_apellido', 'demandados.demandado_dni')
            ->where(function ($query) use ($q) {
                $query->where('demandado_nombre', 'like', "%{$q}%")
                    ->orWhere('demandado_apellido', 'like', "%{$q}%")
                    ->orWhere('demandado_dni', 'like', "%{$q}%");
            })
            ->where('demandado_estado', 'activo');

        if ($esProcurador && $procuradorId) {
            $demandadosQuery->whereHas('casos', function ($query) use ($procuradorId) {
                $query->where('procurador_id', $procuradorId);
            });
        }

        $demandados = $demandadosQuery->limit(5)->get();

        foreach ($demandados as $demandado) {
            $results[] = [
                'type' => 'Demandado',
                'type_icon' => 'user',
                'label' => "{$demandado->demandado_nombre} {$demandado->demandado_apellido}",
                'sub' => "DNI: {$demandado->demandado_dni}",
                'url' => route('demandados.show', $demandado->demandado_dni),
            ];
        }

        return response()->json(['results' => $results]);
    }
}
