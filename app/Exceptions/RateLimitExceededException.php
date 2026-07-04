<?php

namespace App\Exceptions;

class RateLimitExceededException extends AuthenticationException
{
    protected $message = 'Demasiados intentos de inicio de sesión. Por favor, intente nuevamente más tarde.';
}
