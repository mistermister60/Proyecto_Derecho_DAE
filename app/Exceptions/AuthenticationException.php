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


