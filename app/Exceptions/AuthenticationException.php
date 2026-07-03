<?php

namespace App\Exceptions;

use Exception;

class AuthenticationException extends Exception
{
    protected $message = 'Credenciales de autenticación inválidas.';

    public function __construct(
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message ?: $this->message, $code, $previous);
    }
}

class AccountInactiveException extends AuthenticationException
{
    protected $message = 'Su cuenta está desactivada.';
}

class InvalidCredentialsException extends AuthenticationException
{
    protected $message = 'Las credenciales proporcionadas son incorrectas.';
}

class RateLimitExceededException extends AuthenticationException
{
    protected $message = 'Demasiados intentos de inicio de sesión. Por favor, intente nuevamente más tarde.';
}
