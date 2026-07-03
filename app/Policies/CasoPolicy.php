<?php

namespace App\Policies;

use App\Enums\RolEnum;
use App\Models\Caso;
use App\Models\Usuario;

class CasoPolicy
{
    public function view(Usuario $usuario, Caso $caso): bool
    {
        return $this->esDirector($usuario) || $this->esProcuradorAsignado($usuario, $caso);
    }

    public function update(Usuario $usuario, Caso $caso): bool
    {
        return $this->esDirector($usuario) || $this->esProcuradorAsignado($usuario, $caso);
    }

    public function delete(Usuario $usuario, Caso $caso): bool
    {
        return $this->esDirector($usuario);
    }

    public function reasignar(Usuario $usuario, Caso $caso): bool
    {
        return $this->esDirector($usuario);
    }

    public function agregarSeguimiento(Usuario $usuario, Caso $caso): bool
    {
        return $this->esDirector($usuario) || $this->esProcuradorAsignado($usuario, $caso);
    }

    private function esDirector(Usuario $usuario): bool
    {
        return RolEnum::equals($usuario->rol?->rol_nombre, RolEnum::DIRECTOR);
    }

    private function esProcuradorAsignado(Usuario $usuario, Caso $caso): bool
    {
        return $usuario->procurador_id !== null
            && $caso->procurador_id === $usuario->procurador_id;
    }
}
