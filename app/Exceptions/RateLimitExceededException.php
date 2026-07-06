<?php

namespace App\Exceptions;

/**
 * RateLimitExceededException — Límite de intentos de autenticación excedido.
 *
 * Se lanza cuando un usuario supera el número máximo de intentos
 * de inicio de sesión permitidos en un período de tiempo determinado,
 * como medida de seguridad contra ataques de fuerza bruta.
 *
 * @extends AuthenticationException
 */
class RateLimitExceededException extends AuthenticationException
{
    /**
     * Mensaje de error por defecto para límite de intentos excedido.
     *
     * @var string
     */
    protected $message = 'Demasiados intentos de inicio de sesión. Por favor, intente nuevamente más tarde.';
}
