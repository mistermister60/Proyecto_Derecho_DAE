<?php

namespace App\Enums;

/**
 * Estados posibles de un caso estudiantil en el sistema DAE.
 *
 * Representa el ciclo de vida de un caso dentro de la Dirección de Asuntos
 * Estudiantiles. Un caso inicia en estado ACTIVO y, una vez resuelto o
 * archivado, pasa a estado CERRADO.
 */
enum CasoEstadoEnum: string
{
    /** El caso se encuentra activo y en seguimiento. Es el estado inicial
     * de todo caso registrado; implica que está siendo atendido por el
     * personal de la DAE. */
    case ACTIVO = 'activo';

    /** El caso ha sido cerrado. Ya no se realizan acciones sobre él, ya sea
     * porque se resolvió, se archivó o se dio por concluido. */
    case CERRADO = 'cerrado';

    /**
     * Retorna un arreglo con todos los valores válidos del enum.
     *
     * Útil para reglas de validación (exists, in) o para poblar selects
     * en formularios sin tener que escribir los valores manualmente.
     *
     * @return list<string> Lista de valores string de cada case del enum.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
