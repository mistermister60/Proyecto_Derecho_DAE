<?php

namespace App\Exceptions;

class InvalidCredentialsException extends AuthenticationException
{
    protected $message = 'Las credenciales proporcionadas son incorrectas.';
}
