<?php

namespace App\Exceptions\Base;

use \Illuminate\Auth\Access\AuthorizationException as BaseAuthorizationException;
use Throwable;

class AuthorizationException extends BaseAuthorizationException
{
    public function __construct($message = null, $code = 403, Throwable $previous = null)
    {
        parent::__construct($message ?? 'This action is unauthorized.', $code, $previous);
    }
}
