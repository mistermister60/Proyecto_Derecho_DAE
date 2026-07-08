<?php

namespace App\Exceptions;

/**
 * InvalidCredentialsException — Credenciales de autenticación incorrectas.
 *
 * Se lanza cuando el usuario proporciona un email, contraseña u otra
 * credencial que no coincide con los registros del sistema.
 *
 * @extends AuthenticationException
 */
class InvalidCredentialsException extends AuthenticationException
{
    /**
     * Mensaje de error por defecto para credenciales incorrectas.
     *
     * @var string
     */
    protected $message = 'Las credenciales proporcionadas son incorrectas.';
}
