<?php

namespace App\Enums;

/**
 * Roles de usuarios dentro del sistema de gestión de la Dirección de Asuntos Estudiantiles (DAE).
 *
 * Define los perfiles de acceso y responsabilidades que puede tener un usuario
 * en el sistema. Cada rol representa un nivel distinto de permisos y alcance
 * dentro de la gestión de casos estudiantiles.
 */
enum RolEnum: string
{
    /** Director(a) de la DAE. Tiene permisos de administración total sobre
     * los casos: puede crear, asignar, modificar y cerrar cualquier caso. */
    case DIRECTOR = 'director';

    /** Procurador(a) estudiantil. Puede dar seguimiento a los casos asignados,
     * registrar avances y actualizar información, pero con alcance limitado
     * en comparación con el rol de Director. */
    case PROCURADOR = 'procurador';

    /**
     * Compara un valor escalar opcional contra una instancia del enum.
     *
     * Evalúa si el string proporcionado coincide (sin distinción de mayúsculas/
     * minúsculas) con el valor del case del enum. Útil para validar roles
     * provenientes de formularios, sesión o bases de datos.
     *
     * @param  string|null  $value  Valor a comparar (p. ej. 'Director', 'PROCURADOR').
     * @param  self  $enum  Instancia del enum contra la cual comparar.
     * @return bool True si coinciden, false si $value es nulo o no coincide.
     */
    public static function equals(?string $value, self $enum): bool
    {
        if (! $value) {
            return false;
        }

        return strtolower($value) === $enum->value;
    }
}
