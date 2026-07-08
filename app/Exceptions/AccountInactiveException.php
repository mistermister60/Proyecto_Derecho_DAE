<?php

namespace App\Exceptions;

/**
 * AccountInactiveException — Cuenta de usuario desactivada.
 *
 * Se lanza cuando un usuario intenta autenticarse pero su cuenta
 * se encuentra desactivada o suspendida, impidiendo el acceso al sistema.
 *
 * @extends AuthenticationException
 */
class AccountInactiveException extends AuthenticationException
{
    /**
     * Mensaje de error por defecto para cuenta inactiva.
     *
     * @var string
     */
    protected $message = 'Su cuenta está desactivada.';
}
