<?php

namespace App\Policies;

use App\Enums\RolEnum;
use App\Models\Caso;
use App\Models\Usuario;

/**
 * Policy de autorización para el modelo Caso.
 *
 * Define las reglas de acceso sobre los casos judiciales del sistema de gestión
 * de despachos de la Dirección de Asuntos Estudiantiles (DAE). Las operaciones
 * se controlan en función del rol del usuario y, cuando corresponde, de la
 * asignación del caso a un procurador específico.
 *
 * - El rol **Director** tiene acceso total sobre cualquier caso.
 * - El rol **Procurador** solo accede a los casos que le han sido asignados.
 */
class CasoPolicy
{
    /**
     * Determina si el usuario puede visualizar un caso.
     *
     * @param  Usuario  $usuario  Usuario autenticado que solicita la acción.
     * @param  Caso  $caso  Caso que se desea visualizar.
     * @return bool True si es Director o el Procurador asignado al caso.
     */
    public function view(Usuario $usuario, Caso $caso): bool
    {
        return $this->esDirector($usuario) || $this->esProcuradorAsignado($usuario, $caso);
    }

    /**
     * Determina si el usuario puede actualizar un caso.
     *
     * @param  Usuario  $usuario  Usuario autenticado que solicita la acción.
     * @param  Caso  $caso  Caso que se desea modificar.
     * @return bool True si es Director o el Procurador asignado al caso.
     */
    public function update(Usuario $usuario, Caso $caso): bool
    {
        return $this->esDirector($usuario) || $this->esProcuradorAsignado($usuario, $caso);
    }

    /**
     * Determina si el usuario puede eliminar un caso.
     *
     * Solo el Director tiene permisos para eliminar casos del sistema.
     *
     * @param  Usuario  $usuario  Usuario autenticado que solicita la acción.
     * @param  Caso  $caso  Caso que se desea eliminar.
     * @return bool True si es Director.
     */
    public function delete(Usuario $usuario, Caso $caso): bool
    {
        return $this->esDirector($usuario);
    }

    /**
     * Determina si el usuario puede reasignar un caso a otro procurador.
     *
     * Solo el Director tiene permisos para reasignar casos.
     *
     * @param  Usuario  $usuario  Usuario autenticado que solicita la acción.
     * @param  Caso  $caso  Caso que se desea reasignar.
     * @return bool True si es Director.
     */
    public function reasignar(Usuario $usuario, Caso $caso): bool
    {
        return $this->esDirector($usuario);
    }

    /**
     * Determina si el usuario puede agregar un seguimiento a un caso.
     *
     * @param  Usuario  $usuario  Usuario autenticado que solicita la acción.
     * @param  Caso  $caso  Caso al que se desea agregar seguimiento.
     * @return bool True si es Director o el Procurador asignado al caso.
     */
    public function agregarSeguimiento(Usuario $usuario, Caso $caso): bool
    {
        return $this->esDirector($usuario) || $this->esProcuradorAsignado($usuario, $caso);
    }

    /**
     * Verifica si el usuario tiene el rol de Director de la DAE.
     *
     * @param  Usuario  $usuario  Usuario a evaluar.
     * @return bool True si el rol del usuario es DIRECTOR.
     */
    private function esDirector(Usuario $usuario): bool
    {
        return RolEnum::equals($usuario->rol?->rol_nombre, RolEnum::DIRECTOR);
    }

    /**
     * Verifica si el usuario es el procurador asignado al caso.
     *
     * Un procurador solo puede acceder a los casos donde su `procurador_id`
     * coincida con el `procurador_id` del caso. Esta verificación requiere que
     * el usuario tenga un procurador asociado (no nulo).
     *
     * @param  Usuario  $usuario  Usuario a evaluar.
     * @param  Caso  $caso  Caso contra el cual verificar la asignación.
     * @return bool True si el usuario es el procurador asignado al caso.
     */
    private function esProcuradorAsignado(Usuario $usuario, Caso $caso): bool
    {
        return $usuario->procurador_id !== null
            && $caso->procurador_id === $usuario->procurador_id;
    }
}
