<?php

namespace App\Exceptions;

class AccountInactiveException extends AuthenticationException
{
    protected $message = 'Su cuenta está desactivada.';
}
