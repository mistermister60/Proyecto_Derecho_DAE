<?php

namespace App\Enums;

enum RolEnum: string
{
    case DIRECTOR = 'director';
    case PROCURADOR = 'procurador';

    public static function equals(?string $value, self $enum): bool
    {
        if (!$value) return false;
        return strtolower($value) === $enum->value;
    }
}