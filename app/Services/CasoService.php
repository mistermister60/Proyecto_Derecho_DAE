<?php

namespace App\Services;

use App\Models\Caso;
use App\Models\EstadoCaso;
use App\Models\Reasignacion;
use App\Models\TipoTramite;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * CasoService — Servicio de gestión de casos legales del sistema DAE.
 *
 * Provee las operaciones centrales del módulo de casos: listado con soporte
 * de vista Kanban, creación con generación automática de número de expediente
 * (formato: 0501-AÑO-CORRELATIVO), actualización, desactivación lógica y
 * reasignación de procuradores con registro de auditoría en transacciones.
 *
 * La generación de expediente sigue el estándar del sistema DAE donde
 * "0501" es la oficina receptora, seguido del año y un correlativo de 5 dígitos.
 */
class CasoService
{
    /**
     * Obtener los datos estructurados para la vista principal (tabla + Kanban).
     *
     * Orquesta la obtención de datos delegando en métodos especializados:
     * - getTableData(): paginación para la vista de tabla.
     * - getKanbanColumns(): columnas agrupadas para el tablero Kanban.
     *
     * Retorna un array con:
     * - `casos`: paginación de 20 registros con relaciones (cliente, tipoTrámite, estado, procurador, audiencias).
     * - `estados`: lista de estados tipo "pipeline" ordenados por prioridad.
     * - `tramites`: catálogo completo de tipos de trámite.
     * - `columnas`: estructura agrupada para el tablero Kanban, donde cada estado
     *    es una columna con su color y las tarjetas son los casos asociados.
     *
     * Si el usuario autenticado es "Procurador", filtra automáticamente
     * los casos asignados a su procurador_id.
     *
     * @return array<string, mixed> Array con casos, estados, tramites y columnas.
     */
    public function getIndexData(): array
    {
        $user = Auth::user();
        $esProcurador = ($user?->rol?->rol_nombre === 'Procurador');

        $base = Caso::when($esProcurador, fn ($q) => $q->where('procurador_id', $user?->procurador_id));

        $casos = $this->getTableData(clone $base, $esProcurador);
        $estados = EstadoCaso::where('estado_tipo', 'pipeline')
            ->orderBy('estado_orden')
            ->get();
        $tramites = TipoTramite::all();
        $columnas = $this->getKanbanColumns(clone $base);

        return compact('casos', 'estados', 'tramites', 'columnas');
    }

    /**
     * Obtener los datos paginados para la vista de tabla.
     *
     * Aplica las relaciones eager-loaded, ordenamiento y paginación
     * sobre el query base (que ya incluye los filtros de rol).
     *
     * @param  Builder  $base  Query base pre-filtrada.
     * @param  bool  $esProcurador  Indica si el usuario autenticado es Procurador.
     * @return LengthAwarePaginator Paginación de 20 registros por página.
     */
    private function getTableData($base, bool $esProcurador): LengthAwarePaginator
    {
        return $base
            ->with(['cliente', 'tipoTramite', 'estado', 'procurador', 'audiencias'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
    }

    /**
     * Construir las columnas del tablero Kanban agrupadas por estado.
     *
     * Obtiene los casos con relaciones ligeras, los estados de tipo pipeline,
     * y agrupa las tarjetas (casos) dentro de cada columna (estado).
     *
     * @param  Builder  $base  Query base pre-filtrada.
     * @return array<string, array> Array de columnas donde cada clave es el nombre del estado
     *                              y el valor es [color, tarjetas].
     */
    private function getKanbanColumns($base): array
    {
        $casosKanban = (clone $base)
            ->with([
                'cliente:cliente_id,cliente_nombre,cliente_apellido',
                'tipoTramite:tipo_tramite_id,tramite_nombre',
                'audiencias:audiencia_id,caso_id,audiencia_fecha',
            ])
            ->select('caso_id', 'caso_numero_expediente', 'estado_id', 'cliente_id', 'tipo_tramite_id', 'procurador_id')
            ->get();

        $estados = EstadoCaso::where('estado_tipo', 'pipeline')
            ->orderBy('estado_orden')
            ->get();

        $columnas = [];
        foreach ($estados as $estado) {
            $tarjetas = [];
            foreach ($casosKanban->where('estado_id', $estado->estado_id) as $caso) {
                $tarjetas[$caso->caso_numero_expediente] = [
                    $caso->cliente?->nombre_completo ?? 'Sin cliente',
                    $caso->tipoTramite?->tramite_nombre ?? 'Sin trámite',
                    optional($caso->audiencias->first())->audiencia_fecha ?? '',
                ];
            }
            $columnas[$estado->estado_nombre] = [$estado->estado_color, $tarjetas];
        }

        return $columnas;
    }

    /**
     * Crear un nuevo caso con generación automática de número de expediente.
     *
     * El expediente se genera con el formato: 0501-{AÑO}-{CORRELATIVO_5D}.
     * - "0501": código de oficina del sistema DAE.
     * - Año actual (ej. 2026).
     * - Correlativo de 5 dígitos que se incrementa desde el último caso registrado.
     *
     * La generación del expediente y la creación del caso se ejecutan dentro
     * de una transacción de base de datos con bloqueo pesimista (lockForUpdate)
     * para evitar race conditions cuando múltiples usuarios crean casos
     * simultáneamente.
     *
     * **Requisito**: El motor de almacenamiento de la tabla `casos` DEBE ser
     * InnoDB para que `lockForUpdate()` funcione correctamente.
     *
     * Además asigna automáticamente:
     * - Estado inicial "Entrevista".
     * - Fecha de interposición y asignación como la fecha actual.
     * - Estado del caso como "activo".
     *
     * @param  array<string, mixed>  $data  Datos del caso enviados desde el formulario.
     * @return Caso Modelo del caso recién creado.
     *
     * @throws \Throwable Si ocurre un error durante la transacción.
     */
    public function createCaso(array $data): Caso
    {
        return DB::transaction(function () use ($data) {
            // Bloquear la fila para evitar lecturas concurrentes
            $ultimo = Caso::orderBy('caso_id', 'desc')
                ->lockForUpdate()
                ->first();

            $correlativo = $ultimo ? intval(substr($ultimo->caso_numero_expediente, -5)) + 1 : 1;

            $data['caso_numero_expediente'] = '0501-'.now()->year.'-'.str_pad($correlativo, 5, '0', STR_PAD_LEFT);
            $data['estado_id'] = EstadoCaso::where('estado_nombre', 'Entrevista')->value('estado_id');
            $data['caso_fecha_interpuesta'] = now()->toDateString();
            $data['caso_fecha_asignacion'] = now()->toDateString();
            $data['caso_estado'] = 'activo';

            return Caso::create($data);
        });
    }

    /**
     * Actualizar los datos de un caso existente.
     *
     * @param  Caso  $caso  Modelo del caso a actualizar.
     * @param  array<string, mixed>  $data  Datos a modificar (merge sobre los existentes).
     * @return bool True si la actualización fue exitosa.
     */
    public function updateCaso(Caso $caso, array $data): bool
    {
        return $caso->update($data);
    }

    /**
     * Desactivar un caso de forma lógica (soft delete del sistema DAE).
     *
     * En lugar de eliminar el registro, marca el campo `caso_estado` como
     * "inactivo". El caso permanece en la base de datos con su expediente
     * y relaciones para efectos de auditoría y consulta histórica.
     *
     * @param  Caso  $caso  Modelo del caso a desactivar.
     * @return bool True si la desactivación fue exitosa.
     */
    public function deactivateCaso(Caso $caso): bool
    {
        return $caso->update(['caso_estado' => 'inactivo']);
    }

    /**
     * Cerrar un caso con resolución.
     *
     * Marca el caso como 'cerrado' y registra el tipo de resolución,
     * la fecha y las notas correspondientes.
     *
     * @param  Caso  $caso  Modelo del caso a cerrar.
     * @param  array<string, mixed>  $data  Datos de resolución (resolucion_tipo, resolucion_fecha, resolucion_notas).
     * @return bool True si el cierre fue exitoso.
     */
    public function closeCaso(Caso $caso, array $data): bool
    {
        return $caso->update([
            'caso_estado' => 'cerrado',
            'resolucion_tipo' => $data['resolucion_tipo'],
            'resolucion_fecha' => $data['resolucion_fecha'] ?? now()->toDateString(),
            'resolucion_notas' => $data['resolucion_notas'] ?? null,
        ]);
    }

    /**
     * Reasignar un caso a un nuevo procurador con registro de auditoría.
     *
     * Ejecuta toda la operación dentro de una transacción de base de datos:
     * 1. Crea un registro en la tabla `reasignaciones` con el procurador
     *    origen, destino, motivo y fecha.
     * 2. Actualiza el `procurador_id` del caso al nuevo procurador.
     *
     * Si cualquiera de los dos pasos falla, la transacción revierte
     * automáticamente para mantener la consistencia.
     *
     * @param  Caso  $caso  Modelo del caso a reasignar.
     * @param  array<string, mixed>  $data  Debe contener 'procurador_destino_id' y 'reasignacion_motivo'.
     * @return Caso Modelo del caso con el procurador actualizado.
     *
     * @throws \Throwable Si ocurre un error durante la transacción.
     */
    public function reassignCaso(Caso $caso, array $data): Caso
    {
        return DB::transaction(function () use ($caso, $data) {
            // Registrar la reasignación
            Reasignacion::create([
                'caso_id' => $caso->caso_id,
                'procurador_origen_id' => $caso->procurador_id,
                'procurador_destino_id' => $data['procurador_destino_id'],
                'reasignacion_motivo' => $data['reasignacion_motivo'],
                'reasignacion_fecha' => now(),
                'reasignacion_estado' => 'completada',
            ]);

            // Actualizar el procurador del caso
            $caso->update(['procurador_id' => $data['procurador_destino_id']]);

            return $caso;
        });
    }
}
