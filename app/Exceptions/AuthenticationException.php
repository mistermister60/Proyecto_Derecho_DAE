<?php

namespace App\Exceptions;

use Exception;

/**
 * AuthenticationException — Excepción base del dominio de autenticación.
 *
 * Sirve como clase padre para todas las excepciones relacionadas con
 * el proceso de autenticación (credenciales inválidas, cuenta inactiva,
 * límite de intentos, etc.). Si no se proporciona un mensaje personalizado,
 * se utiliza el mensaje por defecto definido en la propiedad $message.
 */
class AuthenticationException extends Exception
{
    /**
     * Mensaje de error por defecto.
     *
     * Se utiliza cuando el constructor recibe una cadena vacía como $message.
     *
     * @var string
     */
    protected $message = 'Credenciales de autenticación inválidas.';

    /**
     * @param  string  $message  Mensaje descriptivo del error (opcional).
     *                           Si está vacío, se usa el valor de $message por defecto.
     * @param  int  $code  Código numérico del error (opcional).
     * @param  \Throwable|null  $previous  Excepción anterior para el encadenamiento (opcional).
     */
    public function __construct(
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message ?: $this->message, $code, $previous);
    }
}
