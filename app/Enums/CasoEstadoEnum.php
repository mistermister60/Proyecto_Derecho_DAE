<?php

namespace App\Enums;

enum CasoEstadoEnum: string
{
    case ACTIVO = 'activo';
    case CERRADO = 'cerrado';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
